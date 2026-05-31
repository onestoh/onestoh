<?php

namespace App\Mail;

use App\Models\RentPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentPaymentReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public RentPayment $payment)
    {
    }

    public function envelope(): Envelope
    {
        $monthYear = $this->payment->paid_at
            ? $this->payment->paid_at->format('F Y')
            : now()->format('F Y');

        return new Envelope(
            subject: "Rent Payment Confirmed — {$monthYear}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rent-payment-received',
            with: [
                'amount'       => 'KES ' . number_format($this->payment->amount, 2),
                'propertyName' => optional($this->payment->lease?->property)->title ?? 'Your Property',
                'tenantName'   => optional($this->payment->tenant)->name ?? 'Tenant',
                'month'        => $this->payment->paid_at?->format('F Y') ?? now()->format('F Y'),
                'reference'    => $this->payment->transaction_ref ?? 'REF-' . str_pad($this->payment->id, 8, '0', STR_PAD_LEFT),
            ],
        );
    }
}
