<?php
namespace App\Http\Middleware;

use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantResolver
{
    public function __construct(private TenantService $tenantService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->tenantService->resolveTenant($request);

        if ($tenant) {
            app()->instance('tenant', $tenant);
            config(['app.tenant_id' => $tenant->id]);
        }

        return $next($request);
    }
}
