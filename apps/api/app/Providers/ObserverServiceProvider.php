<?php
namespace App\Providers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\SaleOffer;
use App\Observers\BookingObserver;
use App\Observers\PaymentObserver;
use App\Observers\SaleOfferObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Booking::observe(BookingObserver::class);
        Payment::observe(PaymentObserver::class);
        SaleOffer::observe(SaleOfferObserver::class);
    }
}
