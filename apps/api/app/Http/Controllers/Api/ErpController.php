<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ErpIntegration;
use App\Services\ErpIntegrationService;
use App\Jobs\SyncErpDataJob;
use Illuminate\Http\{JsonResponse, Request};

class ErpController extends Controller
{
    public function __construct(private ErpIntegrationService $service) {}

    /**
     * List supported ERP platforms with OAuth connect URLs.
     */
    public function getSupportedPlatforms(): JsonResponse
    {
        $platforms = [
            [
                'id'          => 'quickbooks',
                'name'        => 'QuickBooks Online',
                'logo'        => 'https://cdn.theonlineyard.co.ke/logos/quickbooks.svg',
                'connect_url' => 'https://appcenter.intuit.com/connect/oauth2?client_id=' . config('erp.quickbooks.client_id') . '&scope=com.intuit.quickbooks.accounting&redirect_uri=' . urlencode(config('erp.quickbooks.redirect_uri')) . '&response_type=code&state=QB',
            ],
            [
                'id'          => 'xero',
                'name'        => 'Xero',
                'logo'        => 'https://cdn.theonlineyard.co.ke/logos/xero.svg',
                'connect_url' => 'https://login.xero.com/identity/connect/authorize?response_type=code&client_id=' . config('erp.xero.client_id') . '&redirect_uri=' . urlencode(config('erp.xero.redirect_uri')) . '&scope=openid+profile+email+accounting.transactions&state=XERO',
            ],
            ['id' => 'sage',       'name' => 'Sage Business Cloud',  'connect_url' => null, 'available' => false],
            ['id' => 'wave',       'name' => 'Wave Accounting',      'connect_url' => null, 'available' => false],
            ['id' => 'zoho_books', 'name' => 'Zoho Books',           'connect_url' => null, 'available' => false],
        ];
        return response()->json(['data' => $platforms]);
    }

    /**
     * Handle OAuth callback for a platform.
     */
    public function connect(Request $request, string $platform): JsonResponse
    {
        $request->validate(['code' => 'required|string']);
        abort_if(!in_array($platform, ['quickbooks', 'xero']), 400, 'Unsupported platform.');

        $integration = $this->service->connect(auth()->user(), $platform, $request->code);
        return response()->json(['data' => $integration, 'message' => ucfirst($platform) . ' connected successfully.']);
    }

    /**
     * Get the user's ERP integration statuses.
     */
    public function getStatus(): JsonResponse
    {
        $integrations = ErpIntegration::where('user_id', auth()->id())
            ->select(['id', 'platform', 'is_active', 'last_synced_at', 'records_synced', 'last_error', 'token_expires_at'])
            ->get()
            ->map(fn ($i) => array_merge($i->toArray(), [
                'token_expired' => $i->isTokenExpired(),
            ]));

        return response()->json(['data' => $integrations]);
    }

    /**
     * Manually trigger a sync for an integration.
     */
    public function triggerSync(ErpIntegration $integration): JsonResponse
    {
        abort_if($integration->user_id !== auth()->id(), 403);
        SyncErpDataJob::dispatch($integration);
        return response()->json(['message' => 'Sync queued. You will be notified on completion.']);
    }

    /**
     * Retrieve recent sync logs for an integration.
     */
    public function getSyncLogs(ErpIntegration $integration): JsonResponse
    {
        abort_if($integration->user_id !== auth()->id(), 403);
        $logs = $integration->syncLogs()->orderByDesc('created_at')->limit(50)->get();
        return response()->json(['data' => $logs]);
    }

    /**
     * Disconnect and remove an ERP integration.
     */
    public function disconnect(ErpIntegration $integration): JsonResponse
    {
        abort_if($integration->user_id !== auth()->id(), 403);
        $integration->delete();
        return response()->json(['message' => 'ERP integration disconnected.']);
    }
}
