<?php
namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class TenantService
{
    public function createTenant(array $data): Tenant
    {
        return DB::transaction(function () use ($data) {
            $tenant = Tenant::create([
                'name'                   => $data['name'],
                'slug'                   => $data['slug'],
                'domain'                 => $data['domain'] ?? null,
                'logo_url'               => $data['logo_url'] ?? null,
                'primary_color'          => $data['primary_color'] ?? '#E8922A',
                'country_code'           => $data['country_code'] ?? 'KE',
                'default_currency'       => $data['default_currency'] ?? 'KES',
                'timezone'               => $data['timezone'] ?? 'Africa/Nairobi',
                'active_payment_gateways'=> $data['active_payment_gateways'] ?? ['mpesa'],
                'platform_fee_pct'       => $data['platform_fee_pct'] ?? 10,
                'referral_commission_pct'=> $data['referral_commission_pct'] ?? 3,
                'plan'                   => $data['plan'] ?? 'starter',
                'monthly_fee'            => $data['monthly_fee'] ?? 0,
                'per_transaction_fee_pct'=> $data['per_transaction_fee_pct'] ?? 0.5,
                'trial_ends_at'          => now()->addDays(30),
                'is_active'              => true,
            ]);

            // Provision admin account if provided
            if (isset($data['admin_user_id'])) {
                TenantAdmin::create([
                    'tenant_id' => $tenant->id,
                    'user_id'   => $data['admin_user_id'],
                    'role'      => 'super_admin',
                ]);

                User::where('id', $data['admin_user_id'])->update(['tenant_id' => $tenant->id]);
            }

            return $tenant;
        });
    }

    public function resolveTenant(Request $request): ?Tenant
    {
        // Check X-Tenant-Domain header first (for custom domains)
        $domain = $request->header('X-Tenant-Domain');
        if ($domain) {
            return Tenant::where('domain', $domain)->where('is_active', true)->first();
        }

        // Subdomain resolution: {slug}.theonlineyard.co.ke
        $host = $request->getHost();
        $parts = explode('.', $host);
        if (count($parts) >= 3) {
            $slug = $parts[0];
            return Tenant::where('slug', $slug)->where('is_active', true)->first();
        }

        return null;
    }

    public function getTenantConfig(Tenant $tenant): array
    {
        return [
            'id'                      => $tenant->id,
            'name'                    => $tenant->name,
            'slug'                    => $tenant->slug,
            'domain'                  => $tenant->domain,
            'branding'                => [
                'logo_url'      => $tenant->logo_url,
                'primary_color' => $tenant->primary_color,
            ],
            'locale'                  => [
                'country_code'     => $tenant->country_code,
                'default_currency' => $tenant->default_currency,
                'timezone'         => $tenant->timezone,
            ],
            'payment_gateways'        => $tenant->active_payment_gateways ?? [],
            'fees'                    => [
                'platform_fee_pct'        => $tenant->platform_fee_pct,
                'referral_commission_pct' => $tenant->referral_commission_pct,
                'per_transaction_fee_pct' => $tenant->per_transaction_fee_pct,
            ],
            'plan'                    => $tenant->plan,
            'is_on_trial'             => $tenant->isOnTrial(),
            'trial_ends_at'           => $tenant->trial_ends_at,
        ];
    }

    public function calculateTenantRevenue(Tenant $tenant, string $period = 'month'): array
    {
        $query = $tenant->bookings()->where('status', 'closed');

        if ($period === 'month') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } elseif ($period === 'year') {
            $query->whereYear('created_at', now()->year);
        }

        $gmv           = $query->sum('total_amount');
        $platformFees  = $gmv * ($tenant->per_transaction_fee_pct / 100);
        $monthlyFee    = $tenant->monthly_fee;
        $totalRevenue  = $platformFees + $monthlyFee;

        return [
            'period'         => $period,
            'gmv'            => $gmv,
            'platform_fees'  => round($platformFees, 2),
            'monthly_fee'    => $monthlyFee,
            'total_revenue'  => round($totalRevenue, 2),
            'booking_count'  => $query->count(),
        ];
    }

    public function provisionPaymentGateway(Tenant $tenant, string $gateway, array $credentials): void
    {
        $encrypted = Crypt::encryptString(json_encode($credentials));

        // Store in a dedicated config store keyed by tenant+gateway
        DB::table('tenant_gateway_credentials')->updateOrInsert(
            ['tenant_id' => $tenant->id, 'gateway' => $gateway],
            ['credentials_enc' => $encrypted, 'updated_at' => now(), 'created_at' => now()]
        );

        // Activate the gateway in tenant's list
        $gateways   = $tenant->active_payment_gateways ?? [];
        $gateways[] = $gateway;
        $tenant->update(['active_payment_gateways' => array_unique($gateways)]);
    }

    public function getTenantStats(): array
    {
        return Tenant::withCount(['users', 'assets', 'bookings'])
            ->get()
            ->map(function ($tenant) {
                return [
                    'id'             => $tenant->id,
                    'name'           => $tenant->name,
                    'plan'           => $tenant->plan,
                    'is_active'      => $tenant->is_active,
                    'users_count'    => $tenant->users_count,
                    'assets_count'   => $tenant->assets_count,
                    'bookings_count' => $tenant->bookings_count,
                    'monthly_revenue'=> $this->calculateTenantRevenue($tenant, 'month')['total_revenue'],
                    'is_on_trial'    => $tenant->isOnTrial(),
                    'created_at'     => $tenant->created_at,
                ];
            })
            ->toArray();
    }
}
