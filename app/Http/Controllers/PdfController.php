<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\RentPayment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function leaseAgreement($leaseId)
    {
        $lease = Lease::with(['property', 'landlord', 'tenant'])->findOrFail($leaseId);

        // Authorization: only landlord or tenant of this lease, or admin
        $userId = session('user_id');
        $role   = session('role');
        if ($role !== 'admin' && $lease->landlord_id !== $userId && $lease->tenant_id !== $userId) {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.lease-agreement', compact('lease'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("lease-agreement-{$lease->id}.pdf");
    }

    public function rentReceipt($paymentId)
    {
        $payment = RentPayment::with(['lease.property', 'tenant', 'landlord'])->findOrFail($paymentId);

        $userId = session('user_id');
        $role   = session('role');
        if ($role !== 'admin' && $payment->tenant_id !== $userId && $payment->landlord_id !== $userId) {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.rent-receipt', compact('payment'))
            ->setPaper('a4', 'portrait');

        $ref = $payment->transaction_ref ?? 'RCP-' . str_pad($payment->id, 8, '0', STR_PAD_LEFT);
        return $pdf->download("rent-receipt-{$ref}.pdf");
    }

    public function ownerStatement($userId, $month)
    {
        // Authorization: landlord can only access own statement
        $sessionUserId = session('user_id');
        $role          = session('role');
        if ($role !== 'admin' && (int) $userId !== $sessionUserId) {
            abort(403);
        }

        $user       = User::findOrFail($userId);
        $properties = $user->properties()->with(['rentPayments' => function ($q) use ($month) {
            $q->where('status', 'paid')
              ->whereYear('paid_at', substr($month, 0, 4))
              ->whereMonth('paid_at', substr($month, 5, 2));
        }])->get();

        $totalCollected = $properties->flatMap->rentPayments->sum('amount');
        $monthLabel     = \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y');

        $pdf = Pdf::loadView('pdf.owner-statement', compact('user', 'properties', 'totalCollected', 'monthLabel', 'month'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("owner-statement-{$month}-{$userId}.pdf");
    }
}
