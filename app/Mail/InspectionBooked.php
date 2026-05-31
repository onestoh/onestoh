<?php

namespace App\Mail;

use App\Models\Inspection;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InspectionBooked extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Inspection $inspection)
    {
    }

    public function envelope(): Envelope
    {
        $title = optional($this->inspection->property)->title ?? 'Property';

        return new Envelope(
            subject: "Inspection Scheduled — {$title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inspection-booked',
            with: [
                'propertyTitle'  => optional($this->inspection->property)->title ?? 'Property',
                'scheduledAt'    => $this->inspection->scheduled_at?->format('l, d F Y \a\t H:i') ?? 'TBD',
                'requesterName'  => optional($this->inspection->requester)->name ?? 'Requester',
                'notes'          => $this->inspection->notes ?? '',
            ],
        );
    }
}
