<?php
namespace App\Services;

use App\Models\CurrencyRate;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MultiCurrencyService
{
    private array $supported = [
        'KES' => ['name' => 'Kenyan Shilling',    'symbol' => 'KSh',  'countries' => ['KE']],
        'UGX' => ['name' => 'Ugandan Shilling',   'symbol' => 'USh',  'countries' => ['UG']],
        'TZS' => ['name' => 'Tanzanian Shilling',  'symbol' => 'TSh',  'countries' => ['TZ']],
        'USD' => ['name' => 'US Dollar',           'symbol' => '$',    'countries' => ['US']],
        'GBP' => ['name' => 'British Pound',       'symbol' => '£',    'countries' => ['GB']],
        'EUR' => ['name' => 'Euro',                'symbol' => '€',    'countries' => ['EU']],
    ];

    public function fetchRates(): void
    {
        $appId = config('services.open_exchange_rates.app_id');
        if (!$appId) {
            Log::warning('Open Exchange Rates app_id not configured.');
            return;
        }

        $response = Http::get("https://openexchangerates.org/api/latest.json", [
            'app_id'  => $appId,
            'symbols' => implode(',', array_keys($this->supported)),
        ]);

        if ($response->failed()) {
            Log::error('Failed to fetch currency rates', ['status' => $response->status()]);
            return;
        }

        $data  = $response->json();
        $rates = $data['rates'] ?? [];
        $usd   = $rates['USD'] ?? 1;
        $now   = now();

        // Convert all rates relative to KES
        $kesPerUsd = $rates['KES'] ?? 130; // fallback

        foreach (array_keys($this->supported) as $target) {
            if ($target === 'KES') continue;
            if (!isset($rates[$target])) continue;

            $rateUsdToTarget = $rates[$target];
            $rateKesToTarget = $rateUsdToTarget / $kesPerUsd;

            CurrencyRate::create([
                'base_currency'   => 'KES',
                'target_currency' => $target,
                'rate'            => $rateKesToTarget,
                'source'          => 'open_exchange_rates',
                'fetched_at'      => $now,
            ]);

            // Also store inverse
            CurrencyRate::create([
                'base_currency'   => $target,
                'target_currency' => 'KES',
                'rate'            => 1 / $rateKesToTarget,
                'source'          => 'open_exchange_rates',
                'fetched_at'      => $now,
            ]);
        }

        Cache::put('currency_rates_last_fetched', $now->toISOString(), now()->addHours(6));
        Log::info('Currency rates updated', ['count' => count($rates)]);
    }

    public function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) return $amount;

        $cacheKey = "currency_rate_{$from}_{$to}";
        $rate = Cache::remember($cacheKey, now()->addHours(6), function () use ($from, $to) {
            return CurrencyRate::where('base_currency', $from)
                ->where('target_currency', $to)
                ->latest('fetched_at')
                ->value('rate');
        });

        if (!$rate) {
            Log::warning("No exchange rate found for {$from} -> {$to}");
            return $amount;
        }

        return round($amount * $rate, 2);
    }

    public function displayPrice(float $kesAmount, User $user): array
    {
        $currency  = $user->preferred_currency ?? 'KES';
        $converted = $this->convert($kesAmount, 'KES', $currency);
        $formatted = $this->formatCurrency($converted, $currency);

        return [
            'amount'    => $converted,
            'currency'  => $currency,
            'formatted' => $formatted,
        ];
    }

    public function formatCurrency(float $amount, string $currency): string
    {
        $info   = $this->supported[$currency] ?? ['symbol' => $currency];
        $symbol = $info['symbol'];

        // Currencies with no decimal places
        $noDecimals = ['UGX', 'TZS'];
        if (in_array($currency, $noDecimals)) {
            return $symbol . ' ' . number_format($amount, 0);
        }

        return $symbol . ' ' . number_format($amount, 2);
    }

    public function getSupportedCurrencies(): array
    {
        return array_map(function ($code, $info) {
            return array_merge($info, ['code' => $code]);
        }, array_keys($this->supported), array_values($this->supported));
    }
}
