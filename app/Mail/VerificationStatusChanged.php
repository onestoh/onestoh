<?php

namespace App\Mail;

use App\Models\Verification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Verification $verification,
        public string $status
    ) {
    }

    public function envelope(): Envelope
    {
        $label = ucfirst($this->status);

        return new Envelope(
            subject: "Your Verification Status: {$label}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verification-status',
            with: [
                'status'     => $this->status,
                'statusLabel'=> ucfirst($this->status),
                'userName'   => optional($this->verification->user)->name ?? 'User',
                'tierType'   => $this->verification->type ?? 'standard',
            ],
        );
    }
}
