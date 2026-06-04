<?php
use Illuminate\Support\Facades\Schedule;
use App\Jobs\ExpireSlotHoldsJob;
use App\Jobs\CalculateTrustScoreJob;
use App\Jobs\FetchCurrencyRatesJob;
use App\Jobs\GenerateDemandForecastsJob;
use App\Jobs\SendLeasePaymentRemindersJob;
use App\Jobs\CheckFinancingApplicationStatusJob;
use App\Jobs\GenerateMarketReportsJob;
use App\Jobs\ExpirePromotionsJob;
use App\Jobs\ProcessCorporateInvoicesJob;
use App\Jobs\RefreshAssetAnalyticsJob;

Schedule::job(new ExpireSlotHoldsJob)->everyFiveMinutes();
Schedule::job(new CalculateTrustScoreJob)->daily();
Schedule::job(new FetchCurrencyRatesJob)->hourly();
Schedule::job(new GenerateDemandForecastsJob)->dailyAt('06:00');
Schedule::job(new SendLeasePaymentRemindersJob)->dailyAt('09:00');
Schedule::job(new CheckFinancingApplicationStatusJob)->hourly();
Schedule::job(new GenerateMarketReportsJob)->weekly();
Schedule::job(new ExpirePromotionsJob)->hourly();
Schedule::job(new ProcessCorporateInvoicesJob)->monthlyOn(1, '08:00');
Schedule::job(new RefreshAssetAnalyticsJob)->hourly();
