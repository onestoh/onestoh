<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminTenantController extends Controller
{
    public function __construct(private TenantService $service) {}

    public function index(): JsonResponse
    {
        $tenants = Tenant::withCount(['users', 'assets', 'bookings'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json(['data' => $tenants]);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:100',
            'slug'                    => 'required|string|max:60|unique:tenants,slug|alpha_dash',
            'domain'                  => 'nullable|string|unique:tenants,domain',
            'logo_url'                => 'nullable|url',
            'primary_color'           => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'country_code'            => 'nullable|string|size:2',
            'default_currency'        => 'nullable|string|size:3',
            'timezone'                => 'nullable|string|max:40',
            'plan'                    => 'nullable|in:starter,growth,enterprise',
            'monthly_fee'             => 'nullable|numeric|min:0',
            'per_transaction_fee_pct' => 'nullable|numeric|min:0|max:20',
            'admin_user_id'           => 'nullable|exists:users,id',
        ]);

        $tenant = $this->service->createTenant($validated);

        return response()->json(['data' => $tenant, 'message' => 'Tenant created successfully'], 201);
    }

    public function show(Tenant $tenant): JsonResponse
    {
        $config = $this->service->getTenantConfig($tenant);
        $revenue = $this->service->calculateTenantRevenue($tenant, 'month');

        return response()->json(['data' => array_merge($config, ['revenue' => $revenue])]);
    }

    public function update(Request $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validate([
            'name'                    => 'sometimes|string|max:100',
            'domain'                  => 'sometimes|nullable|string|unique:tenants,domain,' . $tenant->id,
            'logo_url'                => 'sometimes|nullable|url',
            'primary_color'           => 'sometimes|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active'               => 'sometimes|boolean',
            'plan'                    => 'sometimes|in:starter,growth,enterprise',
            'monthly_fee'             => 'sometimes|numeric|min:0',
            'per_transaction_fee_pct' => 'sometimes|numeric|min:0|max:20',
            'platform_fee_pct'        => 'sometimes|numeric|min:0|max:50',
        ]);

        $tenant->update($validated);

        return response()->json(['data' => $tenant->fresh(), 'message' => 'Tenant updated']);
    }

    public function provisionGateway(Request $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validate([
            'gateway'     => 'required|string|in:mpesa,mtn_momo,stripe,paypal',
            'credentials' => 'required|array',
        ]);

        $this->service->provisionPaymentGateway($tenant, $validated['gateway'], $validated['credentials']);

        return response()->json(['message' => "Gateway '{$validated['gateway']}' provisioned for tenant {$tenant->name}"]);
    }

    public function stats(): JsonResponse
    {
        $stats = $this->service->getTenantStats();
        return response()->json(['data' => $stats]);
    }
}
