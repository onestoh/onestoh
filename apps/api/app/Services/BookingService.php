<?php
namespace App\Services;

use App\Models\Asset;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Jobs\SendBookingNotificationsJob;
use App\Jobs\ReleaseEscrowJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookingService
{
    // Platform fee percentage
    const PLATFORM_FEE_PERCENT = 0.10;
    const BROKER_COMMISSION_PERCENT = 0.05;
    const DRIVER_SURCHARGE_PERCENT  = 0.15;
    const INSURANCE_FEE_PERCENT     = 0.03;

    public function __construct(
        private AvailabilityService $availabilityService,
        private EscrowService $escrowService,
        private NotificationService $notificationService,
    ) {}

    /**
     * Stage 1: Create booking and hold availability slots.
     */
    public function createBooking(User $client, Asset $asset, array $data): Booking
    {
        return DB::transaction(function () use ($client, $asset, $data) {
            // Verify KYC
            if (!$client->isKycApproved()) {
                throw new \DomainException('KYC approval required to make bookings.');
            }

            $startAt = Carbon::parse($data['start_at']);
            $endAt   = Carbon::parse($data['end_at']);

            // Check availability and hold slots
            $this->availabilityService->holdSlots($asset, $startAt, $endAt);

            // Calculate pricing
            $pricing = $this->calculatePricing($asset, $data['duration_type'], $startAt, $endAt, $data);

            // Resolve broker
            $brokerId = null;
            $brokerCommission = 0;
            if (!empty($data['referral_code'])) {
                $broker = User::where('referral_code', $data['referral_code'])
                    ->where('role', 'broker')
                    ->first();
                if ($broker) {
                    $brokerId = $broker->id;
                    $brokerCommission = $pricing['base_amount'] * self::BROKER_COMMISSION_PERCENT;
                }
            }

            $booking = Booking::create([
                'asset_id'                => $asset->id,
                'client_id'               => $client->id,
                'broker_id'               => $brokerId,
                'referral_code'           => $data['referral_code'] ?? null,
                'rental_type'             => $data['rental_type'] ?? 'self_drive',
                'duration_type'           => $data['duration_type'],
                'start_at'                => $startAt,
                'end_at'                  => $endAt,
                'status'                  => 'pending_payment',
                'currency'                => 'KES',
                'base_amount'             => $pricing['base_amount'],
                'security_deposit_amount' => $pricing['security_deposit'],
                'driver_surcharge'        => $pricing['driver_surcharge'],
                'delivery_fee'            => $pricing['delivery_fee'],
                'insurance_fee'           => $pricing['insurance_fee'],
                'platform_fee'            => $pricing['platform_fee'],
                'broker_commission'       => $brokerCommission,
                'total_amount'            => $pricing['total_amount'],
                'pickup_address'          => $data['pickup_address'] ?? null,
                'pickup_latitude'         => $data['pickup_latitude'] ?? null,
                'pickup_longitude'        => $data['pickup_longitude'] ?? null,
                'delivery_address'        => $data['delivery_address'] ?? null,
                'client_notes'            => $data['client_notes'] ?? null,
            ]);

            // Create escrow record
            $this->escrowService->createEscrow($booking);

            // Update slot booking_id
            $asset->availabilitySlots()
                ->where('status', 'pending')
                ->whereNull('booking_id')
                ->update(['booking_id' => $booking->id]);

            Log::info('Booking created', ['booking_id' => $booking->id, 'asset_id' => $asset->id]);

            return $booking;
        });
    }

    /**
     * Stage 2: Mark payment as processing after STK push initiated.
     */
    public function markPaymentProcessing(Booking $booking): void
    {
        $booking->update(['status' => 'payment_processing']);
    }

    /**
     * Stage 3: Handle successful payment — confirm booking.
     */
    public function handlePaymentSuccess(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $booking = $payment->booking;

            // Add to escrow
            $this->escrowService->addFunds($booking, $payment->amount, $payment->type);

            // Check if full amount collected
            $totalPaid = $booking->payments()
                ->where('status', 'completed')
                ->whereIn('type', ['rental', 'deposit', 'insurance'])
                ->sum('amount');

            if ($totalPaid >= $booking->total_amount) {
                $booking->update(['status' => 'confirmed']);
                $booking->availabilitySlots()->update(['status' => 'confirmed']);

                $this->escrowService->markHeld($booking);

                // Notify owner
                SendBookingNotificationsJob::dispatch($booking->id, 'booking_confirmed');

                // Schedule auto-release 24h after booking end
                ReleaseEscrowJob::dispatch($booking->id)
                    ->delay($booking->end_at->addHours(24));
            }
        });
    }

    /**
     * Stage 4-5: Owner notified, client prepared.
     */
    public function advanceStatus(Booking $booking, string $newStatus, User $actor): void
    {
        $allowed = [
            'confirmed'       => ['owner_notified'],
            'owner_notified'  => ['client_prepared'],
            'client_prepared' => ['active'],
            'active'          => ['completed'],
            'completed'       => ['closed'],
        ];

        $currentAllowed = $allowed[$booking->status] ?? [];
        if (!in_array($newStatus, $currentAllowed)) {
            throw new \DomainException("Cannot transition booking from {$booking->status} to {$newStatus}.");
        }

        $updates = ['status' => $newStatus];

        if ($newStatus === 'active') {
            $updates['start_at'] = now();
        }

        if ($newStatus === 'completed') {
            $updates['actual_end_at'] = now();
        }

        $booking->update($updates);
        SendBookingNotificationsJob::dispatch($booking->id, $newStatus);
    }

    /**
     * Cancel booking with appropriate refund logic.
     */
    public function cancelBooking(Booking $booking, User $actor, string $reason): void
    {
        DB::transaction(function () use ($booking, $actor, $reason) {
            $cancelledBy = $actor->isAdmin() ? 'admin' : ($actor->id === $booking->client_id ? 'client' : 'system');

            $booking->update([
                'status'              => "cancelled_by_{$cancelledBy}",
                'cancellation_reason' => $reason,
                'cancelled_at'        => now(),
            ]);

            // Release held slots
            $booking->availabilitySlots()->update(['status' => 'available', 'booking_id' => null]);

            // Trigger refund via escrow service
            $this->escrowService->refund($booking, $cancelledBy);

            SendBookingNotificationsJob::dispatch($booking->id, 'booking_cancelled');
        });
    }

    /**
     * Calculate rental pricing.
     */
    public function calculatePricing(Asset $asset, string $durationType, Carbon $startAt, Carbon $endAt, array $options = []): array
    {
        $hours = max(1, $startAt->diffInHours($endAt));
        $days  = max(1, $startAt->diffInDays($endAt));

        $baseAmount = match ($durationType) {
            'hourly'  => $asset->hourly_rate * $hours,
            'daily'   => $asset->daily_rate * $days,
            'weekly'  => $asset->weekly_rate * ceil($days / 7),
            'monthly' => $asset->monthly_rate * ceil($days / 30),
            default   => throw new \InvalidArgumentException("Unknown duration type: {$durationType}"),
        };

        $isDriver       = ($options['rental_type'] ?? 'self_drive') === 'chauffeur';
        $hasDelivery    = !empty($options['delivery_address']);
        $deliveryKm     = $options['delivery_km'] ?? 0;

        $driverSurcharge = $isDriver ? ($baseAmount * self::DRIVER_SURCHARGE_PERCENT) : 0;
        $deliveryFee     = $hasDelivery ? ($asset->delivery_fee_per_km * $deliveryKm) : 0;
        $insuranceFee    = $baseAmount * self::INSURANCE_FEE_PERCENT;
        $platformFee     = $baseAmount * self::PLATFORM_FEE_PERCENT;
        $securityDeposit = $asset->security_deposit;

        $totalAmount = $baseAmount + $driverSurcharge + $deliveryFee + $insuranceFee + $platformFee + $securityDeposit;

        return compact('baseAmount', 'driverSurcharge', 'deliveryFee', 'insuranceFee', 'platformFee', 'securityDeposit', 'totalAmount');
    }
}
