<?php

namespace App\Http\Controllers;

use App\Mail\RentPaymentReceived;
use App\Models\Lease;
use App\Models\RentPayment;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RentPaymentController extends Controller
{
    /**
     * Initiate a rent payment (M-Pesa STK Push simulation or Bank transfer).
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'lease_id'       => 'required|integer|exists:leases,id',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|in:mpesa,bank',
        ]);

        $userId  = session('user_id');
        $lease   = Lease::findOrFail($request->lease_id);

        $payment = RentPayment::create([
            'lease_id'   => $lease->id,
            'tenant_id'  => $userId,
            'landlord_id'=> $lease->landlord_id,
            'amount'     => $request->amount,
            'method'     => $request->payment_method,
            'status'     => 'pending',
            'due_date'   => now()->endOfMonth(),
        ]);

        if ($request->payment_method === 'mpesa') {
            // ============================================================
            // REAL DARAJA STK PUSH GOES HERE:
            // $response = Http::withToken($accessToken)->post(
            //     'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
            //     [ 'BusinessShortCode' => env('MPESA_SHORTCODE'), ... ]
            // );
            // ============================================================

            // Simulate: log the request
            Log::channel('single')->info('M-Pesa STK Push Simulation', [
                'payment_id' => $payment->id,
                'amount'     => $request->amount,
                'tenant_id'  => $userId,
                'lease_id'   => $lease->id,
                'timestamp'  => now()->toISOString(),
            ]);

            return response()->json([
                'success'    => true,
                'message'    => 'STK Push sent to your phone. Enter your M-Pesa PIN to complete.',
                'payment_id' => $payment->id,
            ]);
        }

        // Bank transfer
        return response()->json([
            'success'        => true,
            'message'        => 'Bank transfer initiated.',
            'payment_id'     => $payment->id,
            'bank_details'   => [
                'account_number' => 'ESTATEYARD-' . $lease->id,
                'bank'           => 'Equity Bank',
                'branch'         => 'Westlands',
                'amount'         => number_format($request->amount, 2),
            ],
        ]);
    }

    /**
     * Simulate M-Pesa callback — update payment to paid.
     * Real Daraja posts to this endpoint after user confirms PIN.
     */
    public function mpesaCallback(Request $request)
    {
        // In production: verify the request signature from Safaricom
        // and parse $request->Body->stkCallback->ResultCode etc.

        $paymentId = $request->input('payment_id');

        if ($paymentId) {
            $payment = RentPayment::where('id', $paymentId)
                ->where('status', 'pending')
                ->first();

            if ($payment) {
                $payment->update([
                    'status'  => 'paid',
                    'paid_at' => now(),
                ]);

                // Fire mail notification
                try {
                    $tenant = $payment->tenant;
                    if ($tenant?->email) {
                        Mail::to($tenant->email)->queue(new RentPaymentReceived($payment));
                    }
                } catch (\Throwable $e) {
                    Log::warning('RentPaymentReceived mail failed: ' . $e->getMessage());
                }

                // In-app notifications
                try {
                    NotificationService::send(
                        $payment->tenant_id,
                        'Rent Payment Confirmed',
                        'Your payment of KES ' . number_format($payment->amount, 0) . ' has been received.',
                        'payment',
                        '/dashboard/tenant'
                    );
                    NotificationService::send(
                        $payment->landlord_id,
                        'Rent Payment Received',
                        'Tenant has paid KES ' . number_format($payment->amount, 0) . ' for your property.',
                        'payment',
                        '/dashboard/landlord'
                    );
                } catch (\Throwable $e) {
                    Log::warning('Rent payment notification failed: ' . $e->getMessage());
                }
            }
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    /**
     * Return paginated payment history for a lease.
     */
    public function history($leaseId)
    {
        $payments = RentPayment::where('lease_id', $leaseId)
            ->latest('paid_at')
            ->paginate(12);

        return response()->json($payments);
    }
}
