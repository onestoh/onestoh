<?php
namespace App\Services;

use App\Models\{Booking, ErpIntegration, ErpSyncLog, User};
use Illuminate\Support\Facades\{Crypt, Http, Log, Notification};
use App\Notifications\ErpSyncFailedNotification;

class ErpIntegrationService
{
    private const OAUTH_URLS = [
        'quickbooks' => [
            'token'   => 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer',
            'refresh' => 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer',
        ],
        'xero' => [
            'token'   => 'https://identity.xero.com/connect/token',
            'refresh' => 'https://identity.xero.com/connect/token',
        ],
    ];

    /**
     * Exchange OAuth code for tokens and create integration record.
     */
    public function connect(User $user, string $platform, string $authCode): ErpIntegration
    {
        $config  = config("erp.{$platform}");
        $tokenUrl = self::OAUTH_URLS[$platform]['token'] ?? null;

        $response = Http::asForm()->post($tokenUrl, [
            'grant_type'   => 'authorization_code',
            'code'         => $authCode,
            'redirect_uri' => $config['redirect_uri'],
            'client_id'    => $config['client_id'],
            'client_secret' => $config['client_secret'],
        ]);

        abort_if(!$response->successful(), 502, 'Failed to exchange OAuth code with ' . $platform);

        $tokens = $response->json();

        return ErpIntegration::updateOrCreate(
            ['user_id' => $user->id, 'platform' => $platform],
            [
                'access_token_encrypted'  => Crypt::encryptString($tokens['access_token']),
                'refresh_token_encrypted' => Crypt::encryptString($tokens['refresh_token'] ?? ''),
                'realm_id'                => $tokens['realmId'] ?? null,
                'tenant_id_erp'           => $tokens['tenantId'] ?? null,
                'token_expires_at'        => now()->addSeconds($tokens['expires_in'] ?? 3600),
                'is_active'               => true,
                'last_error'              => null,
                'sync_settings'           => ['sync_invoices' => true, 'sync_expenses' => true, 'sync_contacts' => true],
            ]
        );
    }

    /**
     * Refresh an expired OAuth token.
     */
    public function refreshToken(ErpIntegration $integration): void
    {
        $config   = config("erp.{$integration->platform}");
        $tokenUrl = self::OAUTH_URLS[$integration->platform]['refresh'] ?? null;

        $response = Http::asForm()->post($tokenUrl, [
            'grant_type'    => 'refresh_token',
            'refresh_token' => Crypt::decryptString($integration->refresh_token_encrypted),
            'client_id'     => $config['client_id'],
            'client_secret' => $config['client_secret'],
        ]);

        abort_if(!$response->successful(), 502, 'Token refresh failed for ' . $integration->platform);

        $tokens = $response->json();
        $integration->update([
            'access_token_encrypted'  => Crypt::encryptString($tokens['access_token']),
            'refresh_token_encrypted' => Crypt::encryptString($tokens['refresh_token'] ?? Crypt::decryptString($integration->refresh_token_encrypted)),
            'token_expires_at'        => now()->addSeconds($tokens['expires_in'] ?? 3600),
        ]);
    }

    /**
     * Push completed booking invoices to the ERP platform.
     * Returns count of successfully synced records.
     */
    public function syncInvoices(ErpIntegration $integration): int
    {
        if ($integration->isTokenExpired()) {
            $this->refreshToken($integration);
            $integration->refresh();
        }

        $settings = $integration->sync_settings ?? [];
        if (empty($settings['sync_invoices'])) {
            return 0;
        }

        $bookings = Booking::where('owner_id', $integration->user_id)
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereDoesntHave('erpSyncLogs', fn ($q) =>
                $q->where('erp_integration_id', $integration->id)
                  ->where('entity_type', 'invoice')
                  ->where('status', 'success')
            )
            ->limit(50)
            ->get();

        $synced = 0;
        foreach ($bookings as $booking) {
            try {
                $payload  = $integration->platform === 'xero'
                    ? $this->getXeroInvoicePayload($booking)
                    : $this->getQuickBooksInvoicePayload($booking);

                $endpoint = $integration->platform === 'xero'
                    ? 'https://api.xero.com/api.xro/2.0/Invoices'
                    : "https://quickbooks.api.intuit.com/v3/company/{$integration->realm_id}/invoice";

                $response = Http::withToken(Crypt::decryptString($integration->access_token_encrypted))
                    ->when($integration->platform === 'xero', fn ($h) => $h->withHeaders(['xero-tenant-id' => $integration->tenant_id_erp]))
                    ->post($endpoint, $payload);

                if ($response->successful()) {
                    $remoteId = $integration->platform === 'xero'
                        ? $response->json('Invoices.0.InvoiceID')
                        : $response->json('Invoice.Id');

                    ErpSyncLog::create([
                        'erp_integration_id' => $integration->id,
                        'direction'          => 'push',
                        'entity_type'        => 'invoice',
                        'local_id'           => (string) $booking->id,
                        'remote_id'          => $remoteId,
                        'status'             => 'success',
                    ]);
                    $synced++;
                } else {
                    throw new \RuntimeException($response->body());
                }
            } catch (\Throwable $e) {
                ErpSyncLog::create([
                    'erp_integration_id' => $integration->id,
                    'direction'          => 'push',
                    'entity_type'        => 'invoice',
                    'local_id'           => (string) $booking->id,
                    'status'             => 'failed',
                    'error_message'      => $e->getMessage(),
                ]);
                Log::warning('ERP invoice sync failed', ['booking' => $booking->id, 'error' => $e->getMessage()]);
            }
        }

        $integration->increment('records_synced', $synced);
        $integration->update(['last_synced_at' => now()]);

        return $synced;
    }

    /**
     * Push maintenance costs as expenses.
     */
    public function syncExpenses(ErpIntegration $integration): int
    {
        if ($integration->isTokenExpired()) {
            $this->refreshToken($integration);
            $integration->refresh();
        }

        $settings = $integration->sync_settings ?? [];
        if (empty($settings['sync_expenses'])) {
            return 0;
        }

        // Placeholder: expenses come from MaintenanceRecord model (Phase 3)
        $expenses = \App\Models\MaintenanceRecord::where('owner_id', $integration->user_id)
            ->whereDoesntHave('erpSyncLogs', fn ($q) =>
                $q->where('erp_integration_id', $integration->id)
                  ->where('entity_type', 'expense')
                  ->where('status', 'success')
            )
            ->limit(50)
            ->get();

        $synced = 0;
        foreach ($expenses as $expense) {
            try {
                $payload  = [
                    'AccountRef' => ['value' => '1'],
                    'Amount'     => $expense->cost,
                    'TxnDate'    => $expense->completed_at?->toDateString() ?? now()->toDateString(),
                    'PrivateNote' => "Maintenance: {$expense->description}",
                ];

                $endpoint = "https://quickbooks.api.intuit.com/v3/company/{$integration->realm_id}/purchase";
                $response = Http::withToken(Crypt::decryptString($integration->access_token_encrypted))
                    ->post($endpoint, $payload);

                $status = $response->successful() ? 'success' : 'failed';
                ErpSyncLog::create([
                    'erp_integration_id' => $integration->id,
                    'direction'          => 'push',
                    'entity_type'        => 'expense',
                    'local_id'           => (string) $expense->id,
                    'remote_id'          => $response->successful() ? $response->json('Purchase.Id') : null,
                    'status'             => $status,
                    'error_message'      => $response->successful() ? null : $response->body(),
                ]);
                if ($response->successful()) {
                    $synced++;
                }
            } catch (\Throwable $e) {
                Log::warning('ERP expense sync failed', ['expense' => $expense->id, 'error' => $e->getMessage()]);
            }
        }

        $integration->increment('records_synced', $synced);
        return $synced;
    }

    /**
     * Sync yard owner customer records as contacts.
     */
    public function syncContacts(ErpIntegration $integration): int
    {
        if ($integration->isTokenExpired()) {
            $this->refreshToken($integration);
            $integration->refresh();
        }

        // Get renters who have bookings with this owner
        $renters = User::whereHas('bookingsAsRenter', fn ($q) => $q->where('owner_id', $integration->user_id))
            ->limit(100)
            ->get();

        $synced = 0;
        foreach ($renters as $renter) {
            try {
                $endpoint = $integration->platform === 'xero'
                    ? 'https://api.xero.com/api.xro/2.0/Contacts'
                    : "https://quickbooks.api.intuit.com/v3/company/{$integration->realm_id}/customer";

                $payload = $integration->platform === 'xero' ? [
                    'Name'         => $renter->name,
                    'EmailAddress' => $renter->email,
                ] : [
                    'DisplayName' => $renter->name,
                    'PrimaryEmailAddr' => ['Address' => $renter->email],
                ];

                $response = Http::withToken(Crypt::decryptString($integration->access_token_encrypted))
                    ->when($integration->platform === 'xero', fn ($h) => $h->withHeaders(['xero-tenant-id' => $integration->tenant_id_erp]))
                    ->post($endpoint, $payload);

                if ($response->successful()) {
                    $synced++;
                }
            } catch (\Throwable $e) {
                Log::warning('ERP contact sync failed', ['renter' => $renter->id, 'error' => $e->getMessage()]);
            }
        }

        return $synced;
    }

    /**
     * Format a booking as a QuickBooks invoice payload.
     */
    public function getQuickBooksInvoicePayload(Booking $booking): array
    {
        return [
            'DocNumber'   => 'TOY-' . $booking->id,
            'TxnDate'     => $booking->created_at->toDateString(),
            'DueDate'     => $booking->end_date,
            'Line'        => [
                [
                    'Amount'          => $booking->total_amount,
                    'DetailType'      => 'SalesItemLineDetail',
                    'Description'     => 'Asset Hire: ' . optional($booking->asset)->name,
                    'SalesItemLineDetail' => [
                        'UnitPrice' => $booking->daily_rate ?? $booking->total_amount,
                        'Qty'       => $booking->rental_days ?? 1,
                    ],
                ],
            ],
            'CustomerRef' => ['value' => (string) $booking->renter_id],
            'BillEmail'   => ['Address' => optional($booking->renter)->email],
        ];
    }

    /**
     * Format a booking as a Xero invoice payload.
     */
    public function getXeroInvoicePayload(Booking $booking): array
    {
        return [
            'Type'        => 'ACCREC',
            'InvoiceNumber' => 'TOY-' . $booking->id,
            'Date'        => $booking->created_at->toDateString(),
            'DueDate'     => $booking->end_date,
            'Status'      => 'AUTHORISED',
            'Contact'     => ['Name' => optional($booking->renter)->name ?? 'Unknown'],
            'LineItems'   => [
                [
                    'Description' => 'Asset Hire: ' . optional($booking->asset)->name,
                    'UnitAmount'  => $booking->daily_rate ?? $booking->total_amount,
                    'Quantity'    => $booking->rental_days ?? 1,
                    'AccountCode' => '200',
                ],
            ],
        ];
    }

    /**
     * Handle a sync error — log and alert owner.
     */
    public function handleSyncError(ErpIntegration $integration, \Exception $e): void
    {
        $integration->update(['last_error' => $e->getMessage()]);
        Log::error('ERP sync error', [
            'integration_id' => $integration->id,
            'platform'       => $integration->platform,
            'error'          => $e->getMessage(),
        ]);
        $integration->user->notify(new ErpSyncFailedNotification($integration, $e->getMessage()));
    }
}
