<?php
namespace App\Services;

use App\Models\Asset;
use App\Models\Booking;
use App\Models\SaleOffer;
use App\Models\TestDriveBooking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SalesService
{
    /**
     * Create a sale enquiry / offer and open a platform message thread.
     */
    public function createEnquiry(Asset $asset, User $buyer, float $offerPrice): SaleOffer
    {
        $offer = SaleOffer::create([
            'asset_id'     => $asset->id,
            'buyer_id'     => $buyer->id,
            'seller_id'    => $asset->owner_id,
            'asking_price' => $asset->sale_price ?? $asset->daily_rate,
            'offer_price'  => $offerPrice,
            'status'       => 'enquiry',
        ]);

        // Open a message thread between buyer and seller
        app(MessageService::class)->openThread($buyer, $asset->owner, [
            'context_type' => 'sale_offer',
            'context_id'   => $offer->id,
        ]);

        $asset->owner->notify(new \App\Notifications\NewSaleEnquiryNotification($offer));

        return $offer;
    }

    /**
     * Submit a counter offer and notify the other party.
     */
    public function counterOffer(SaleOffer $offer, float $newPrice, User $by): SaleOffer
    {
        $offer->update([
            'offer_price' => $newPrice,
            'status'      => 'negotiating',
        ]);

        $notifyUser = $by->id === $offer->buyer_id ? $offer->seller : $offer->buyer;
        $notifyUser->notify(new \App\Notifications\CounterOfferNotification($offer));

        return $offer->fresh();
    }

    /**
     * Both parties agree on a price — generate sale agreement PDF and lock the price.
     */
    public function agreePrice(SaleOffer $offer, float $agreedPrice): SaleOffer
    {
        $offer->update([
            'agreed_price'        => $agreedPrice,
            'status'              => 'price_agreed',
            'payment_deadline'    => now()->addDays(7)->toDateString(),
        ]);

        // Generate PDF (stubbed — replaced with real generator in Phase 3)
        $pdfPath = $this->generateAgreementPdf($offer);
        $offer->update(['agreement_pdf_path' => $pdfPath]);

        $offer->buyer->notify(new \App\Notifications\PriceAgreedNotification($offer));
        $offer->seller->notify(new \App\Notifications\PriceAgreedNotification($offer));

        return $offer->fresh();
    }

    /**
     * Charge 10% reservation fee to escrow and remove asset from rental marketplace.
     */
    public function payReservationFee(SaleOffer $offer, string $paymentMethod): SaleOffer
    {
        $reservationFee = round((float) $offer->agreed_price * 0.10, 2);

        // Charge via payment service
        app(PaymentService::class)->chargeEscrow($offer->buyer, $reservationFee, $paymentMethod, [
            'context_type' => 'sale_offer',
            'context_id'   => $offer->id,
        ]);

        $offer->update([
            'reservation_fee' => $reservationFee,
            'status'          => 'reservation_paid',
        ]);

        // Pull asset from rental availability
        $offer->asset->update(['status' => 'reserved_for_sale']);

        $offer->seller->notify(new \App\Notifications\ReservationReceivedNotification($offer));

        return $offer->fresh();
    }

    /**
     * Mark sale completed on full payment or expire and re-list asset on deadline miss.
     */
    public function completeOrExpireSale(SaleOffer $offer): void
    {
        $deadline = Carbon::parse($offer->payment_deadline);

        if ($deadline->isPast() && $offer->status !== 'completed') {
            $offer->update(['status' => 'expired']);
            $offer->asset->update(['status' => 'available']);
            $offer->buyer->notify(new \App\Notifications\SaleExpiredNotification($offer));
        } elseif ($offer->status === 'balance_due') {
            $offer->update(['status' => 'completed']);
            $offer->asset->update(['status' => 'sold']);
            $offer->buyer->notify(new \App\Notifications\SaleCompletedNotification($offer));
            $offer->seller->notify(new \App\Notifications\SaleCompletedNotification($offer));
        }
    }

    /**
     * Create a test drive booking with a deposit hold.
     */
    public function scheduleTestDrive(Asset $asset, User $client, Carbon $slot): TestDriveBooking
    {
        $depositAmount = 5000; // KES 5,000 deposit

        app(PaymentService::class)->holdDeposit($client, $depositAmount, [
            'context_type' => 'test_drive',
            'asset_id'     => $asset->id,
        ]);

        $testDrive = TestDriveBooking::create([
            'asset_id'       => $asset->id,
            'client_id'      => $client->id,
            'scheduled_at'   => $slot,
            'status'         => 'requested',
            'deposit_amount' => $depositAmount,
        ]);

        $asset->owner->notify(new \App\Notifications\TestDriveRequestedNotification($testDrive));

        return $testDrive;
    }

    private function generateAgreementPdf(SaleOffer $offer): string
    {
        // Stub — returns a placeholder path. Phase 3 will integrate a real PDF generator.
        $filename = 'sale-agreements/offer-' . $offer->id . '-' . now()->timestamp . '.pdf';
        Storage::put($filename, 'PDF_PLACEHOLDER');
        return $filename;
    }
}
