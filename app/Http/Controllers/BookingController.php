<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Listing;
use App\Models\Payment;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmed;
use App\Mail\BookingCancelled;

class BookingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $bookings = Booking::with('listing.primaryPhoto')
            ->where(function ($q) use ($user) {
                $q->where('client_id', $user->id)
                  ->orWhereHas('listing', fn($lq) => $lq->where('user_id', $user->id));
            })
            ->latest()
            ->paginate(15);

        return view('bookings.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        $listing = Listing::with(['photos', 'category'])->active()->findOrFail($request->listing_id);
        $durationType = $request->get('duration_type', 'daily');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        return view('bookings.create', compact('listing', 'durationType', 'startDate', 'endDate'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'listing_id'    => ['required', 'exists:listings,id'],
            'duration_type' => ['required', 'in:hourly,daily,weekly,monthly'],
            'start_datetime' => ['required', 'date', 'after:now'],
            'end_datetime'   => ['required', 'date', 'after:start_datetime'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $listing = Listing::active()->findOrFail($data['listing_id']);

        $start = \Carbon\Carbon::parse($data['start_datetime']);
        $end = \Carbon\Carbon::parse($data['end_datetime']);

        $baseAmount = $this->calculateAmount($listing, $data['duration_type'], $start, $end);
        $platformFeeRate = PlatformSetting::get('platform_fee_percentage', 10) / 100;
        $platformFee = round($baseAmount * $platformFeeRate, 2);
        $securityDeposit = $listing->security_deposit ?? 0;
        $totalAmount = $baseAmount + $platformFee + $securityDeposit;

        $booking = Booking::create([
            'listing_id'      => $listing->id,
            'client_id'       => auth()->id(),
            'duration_type'   => $data['duration_type'],
            'start_datetime'  => $start,
            'end_datetime'    => $end,
            'base_amount'     => $baseAmount,
            'platform_fee'    => $platformFee,
            'security_deposit' => $securityDeposit,
            'total_amount'    => $totalAmount,
            'status'          => 'pending_payment',
            'notes'           => $data['notes'] ?? null,
            'slot_hold_expires_at' => now()->addMinutes(15),
        ]);

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking created! Complete payment within 15 minutes to confirm your slot.');
    }

    public function show(Booking $booking)
    {
        abort_unless(
            $booking->client_id === auth()->id() || $booking->listing?->user_id === auth()->id(),
            403
        );
        $booking->load(['listing.photos', 'listing.user', 'listing.category']);
        return view('bookings.show', compact('booking'));
    }

    private function calculateAmount(Listing $listing, string $durationType, \Carbon\Carbon $start, \Carbon\Carbon $end): float
    {
        return match($durationType) {
            'hourly'  => ceil($start->diffInHours($end)) * ($listing->hourly_rate ?? 0),
            'weekly'  => ceil($start->diffInDays($end) / 7) * ($listing->weekly_rate ?? 0),
            'monthly' => ceil($start->diffInDays($end) / 30) * ($listing->monthly_rate ?? 0),
            default   => ceil($start->diffInDays($end)) * ($listing->daily_rate ?? 0),
        };
    }

    public function pay(Booking $booking)
    {
        abort_unless($booking->client_id === auth()->id(), 403);
        abort_unless($booking->status === 'pending_payment', 403);

        if ($booking->isHoldExpired()) {
            $booking->update(['status' => 'cancelled']);
            return redirect()->route('bookings.index')->with('error', 'Booking expired. Please create a new booking.');
        }

        return view('bookings.pay', compact('booking'));
    }

    public function mpesaPay(Request $request, Booking $booking)
    {
        abort_unless($booking->client_id === auth()->id(), 403);
        abort_unless($booking->status === 'pending_payment', 403);

        $data = $request->validate([
            'phone' => ['required', 'string', 'regex:/^(?:254|\+254|0)?(7[0-9]{8})$/'],
        ]);

        $phone = preg_replace('/^(\+?254|0)/', '254', $data['phone']);
        $amount = (int) ceil($booking->total_amount);

        $stkResult = $this->initiateMpesaStk($phone, $amount, $booking->booking_ref);

        if ($stkResult['success']) {
            Payment::create([
                'booking_id'       => $booking->id,
                'user_id'          => auth()->id(),
                'amount'           => $booking->total_amount,
                'payment_method'   => 'mpesa',
                'status'           => 'pending',
                'gateway_ref'      => $stkResult['checkout_request_id'],
                'gateway_response' => $stkResult['response'],
            ]);

            return back()->with('success', 'M-Pesa prompt sent to ' . $data['phone'] . '. Enter your PIN to complete payment.');
        }

        return back()->withErrors(['phone' => 'M-Pesa request failed. ' . ($stkResult['message'] ?? 'Please try again.')]);
    }

    public function mpesaCallback(Request $request)
    {
        $body = $request->input('Body.stkCallback');
        if (!$body) return response()->json(['status' => 'ok']);

        $checkoutId = $body['CheckoutRequestID'] ?? null;
        $resultCode = $body['ResultCode'] ?? 1;

        $payment = Payment::where('gateway_ref', $checkoutId)->first();
        if (!$payment) return response()->json(['status' => 'ok']);

        if ($resultCode == 0) {
            $meta = collect($body['CallbackMetadata']['Item'] ?? []);
            $receiptNo = $meta->firstWhere('Name', 'MpesaReceiptNumber')['Value'] ?? null;

            $payment->update(['status' => 'completed', 'mpesa_receipt' => $receiptNo]);
            $payment->booking->update(['status' => 'confirmed']);

            // Send booking confirmed email
            try {
                $confirmedBooking = $payment->booking->fresh(['listing', 'client']);
                if ($confirmedBooking?->client?->email) {
                    Mail::to($confirmedBooking->client)->queue(new BookingConfirmed($confirmedBooking));
                }
            } catch (\Exception $e) {
                Log::error('BookingConfirmed email error: ' . $e->getMessage());
            }

            // Credit owner wallet minus platform fee
            $listing = $payment->booking->listing;
            if ($listing?->user) {
                $ownerAmount = $payment->booking->base_amount - ($payment->booking->platform_fee ?? 0);
                $listing->user->wallet?->credit($ownerAmount, 'booking_payment', "Booking {$payment->booking->booking_ref}", $payment->booking->id);
            }
        } else {
            $payment->update(['status' => 'failed', 'gateway_response' => $body]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function start(Booking $booking)
    {
        abort_unless($booking->listing?->user_id === auth()->id(), 403);
        abort_unless($booking->status === 'confirmed', 403);
        $booking->update(['status' => 'active']);
        return back()->with('success', 'Booking marked as active.');
    }

    public function complete(Booking $booking)
    {
        abort_unless($booking->listing?->user_id === auth()->id(), 403);
        abort_unless($booking->status === 'active', 403);
        $booking->update(['status' => 'completed']);
        return back()->with('success', 'Booking marked as completed.');
    }

    public function dispute(Request $request, Booking $booking)
    {
        abort_unless($booking->client_id === auth()->id(), 403);
        $request->validate(['reason' => ['required', 'string', 'min:20']]);
        $booking->update(['status' => 'disputed']);
        \App\Models\Dispute::create([
            'booking_id'   => $booking->id,
            'raised_by'    => auth()->id(),
            'reason'       => $request->reason,
            'status'       => 'open',
        ]);
        return back()->with('success', 'Dispute raised. Our team will review within 48 hours.');
    }

    private function initiateMpesaStk(string $phone, int $amount, string $reference): array
    {
        $consumerKey    = config('services.mpesa.consumer_key', '');
        $consumerSecret = config('services.mpesa.consumer_secret', '');
        $shortcode      = config('services.mpesa.shortcode', '174379');
        $passkey        = config('services.mpesa.passkey', '');
        $env            = config('services.mpesa.env', 'sandbox');
        $baseUrl        = $env === 'production' ? 'https://api.safaricom.co.ke' : 'https://sandbox.safaricom.co.ke';

        try {
            // Get token
            $tokenRes = Http::withBasicAuth($consumerKey, $consumerSecret)
                ->get("{$baseUrl}/oauth/v1/generate?grant_type=client_credentials");
            $token = $tokenRes->json('access_token');

            $timestamp = now()->format('YmdHis');
            $password  = base64_encode($shortcode . $passkey . $timestamp);
            $callbackUrl = url('/payments/mpesa/callback');

            $res = Http::withToken($token)->post("{$baseUrl}/mpesa/stkpush/v1/processrequest", [
                'BusinessShortCode' => $shortcode,
                'Password'          => $password,
                'Timestamp'         => $timestamp,
                'TransactionType'   => 'CustomerPayBillOnline',
                'Amount'            => $amount,
                'PartyA'            => $phone,
                'PartyB'            => $shortcode,
                'PhoneNumber'       => $phone,
                'CallBackURL'       => $callbackUrl,
                'AccountReference'  => $reference,
                'TransactionDesc'   => 'TheOnlineYard Booking',
            ]);

            if ($res->successful() && $res->json('ResponseCode') === '0') {
                return ['success' => true, 'checkout_request_id' => $res->json('CheckoutRequestID'), 'response' => $res->json()];
            }

            return ['success' => false, 'message' => $res->json('errorMessage') ?? 'STK push failed', 'response' => $res->json()];
        } catch (\Exception $e) {
            Log::error('M-Pesa STK error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Payment service unavailable.'];
        }
    }

    public function cancel(Booking $booking)
    {
        abort_unless($booking->client_id === auth()->id(), 403);
        abort_unless(in_array($booking->status, ['pending_payment', 'confirmed']), 403);

        $booking->update(['status' => 'cancelled']);

        // Send booking cancelled email
        try {
            $booking->load(['listing', 'client']);
            if ($booking->client?->email) {
                Mail::to($booking->client)->queue(new BookingCancelled($booking));
            }
        } catch (\Exception $e) {
            Log::error('BookingCancelled email error: ' . $e->getMessage());
        }

        return back()->with('success', 'Booking cancelled.');
    }
}
