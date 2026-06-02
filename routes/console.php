<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    // Mark overdue payments
    \App\Models\RentPayment::where('status', 'pending')
        ->where('due_date', '<', now()->toDateString())
        ->update(['status' => 'overdue']);

    // Notify tenants of overdue payments
    $overdue = \App\Models\RentPayment::where('status', 'overdue')
        ->with(['tenant', 'lease.property'])
        ->get();

    foreach ($overdue as $payment) {
        \App\Services\NotificationService::send(
            $payment->tenant_id,
            '⚠️ Rent Overdue',
            'Your rent of KES ' . number_format($payment->amount) . ' for ' . optional($payment->lease?->property)->title . ' was due on ' . $payment->due_date . '. Please pay immediately to avoid penalties.',
            'payment',
            '/dashboard/tenant'
        );
        \App\Services\NotificationService::send(
            $payment->landlord_id,
            '⚠️ Overdue Rent Alert',
            optional($payment->tenant)->name . ' has not paid KES ' . number_format($payment->amount) . ' due on ' . $payment->due_date . ' for ' . optional($payment->lease?->property)->title,
            'payment',
            '/dashboard/landlord'
        );
    }
})->daily()->name('mark-overdue-rents');

Schedule::call(function () {
    // Auto-end auctions that have passed their end time
    \App\Models\Auction::where('status', 'live')
        ->where('ends_at', '<', now())
        ->each(function($auction) {
            $winningBid = $auction->bids()->where('is_winning', true)->first();
            $auction->update([
                'status'    => 'ended',
                'winner_id' => $winningBid?->bidder_id,
            ]);
            if ($winningBid) {
                \App\Services\NotificationService::send($winningBid->bidder_id, '🏆 You Won!',
                    'You won the auction for ' . optional($auction->property)->title . ' with KES ' . number_format($winningBid->amount),
                    'auction', '/auctions/' . $auction->id);
            }
        });
})->everyMinute()->name('auto-end-auctions');
