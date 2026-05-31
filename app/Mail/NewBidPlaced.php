<?php

namespace App\Mail;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewBidPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Bid $bid,
        public Auction $auction
    ) {
    }

    public function envelope(): Envelope
    {
        $title  = optional($this->auction->property)->title ?? 'Property';
        $amount = number_format($this->bid->amount, 0);

        return new Envelope(
            subject: "New Bid on {$title} — KES {$amount}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-bid',
            with: [
                'propertyTitle' => optional($this->auction->property)->title ?? 'Property',
                'bidAmount'     => 'KES ' . number_format($this->bid->amount, 0),
                'bidderName'    => optional($this->bid->bidder)->name ?? 'Bidder',
                'auctionEndsAt' => $this->auction->ends_at?->format('d F Y H:i') ?? 'TBD',
                'auctionId'     => $this->auction->id,
            ],
        );
    }
}
