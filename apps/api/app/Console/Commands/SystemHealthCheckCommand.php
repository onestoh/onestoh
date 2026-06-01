<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class SystemHealthCheckCommand extends Command
{
    protected $signature = 'system:health-check';
    protected $description = 'Run a comprehensive system health check';

    public function handle(): int
    {
        $this->info('TheOnlineYard System Health Check');
        $this->line('');

        $checks = [
            'Database' => fn() => $this->checkDatabase(),
            'Cache (Redis)' => fn() => $this->checkCache(),
            'Queue' => fn() => $this->checkQueue(),
            'Storage' => fn() => $this->checkStorage(),
            'Environment' => fn() => $this->checkEnvironment(),
        ];

        $allPassed = true;
        foreach ($checks as $name => $check) {
            [$status, $detail] = $check();
            $icon = $status ? '[OK]' : '[FAIL]';
            $this->line("{$icon} {$name}: {$detail}");
            if (!$status) $allPassed = false;
        }

        $this->line('');
        if ($allPassed) {
            $this->info('All systems operational');
            return Command::SUCCESS;
        } else {
            $this->error('Some systems have issues');
            return Command::FAILURE;
        }
    }

    private function checkDatabase(): array
    {
        try {
            $count = DB::table('users')->count();
            return [true, "Connected. Users: {$count}"];
        } catch (\Exception $e) {
            return [false, 'Connection failed: ' . $e->getMessage()];
        }
    }

    private function checkCache(): array
    {
        try {
            Cache::put('health_check', 'ok', 10);
            $val = Cache::get('health_check');
            return [$val === 'ok', $val === 'ok' ? 'Read/write OK' : 'Read/write failed'];
        } catch (\Exception $e) {
            return [false, 'Failed: ' . $e->getMessage()];
        }
    }

    private function checkQueue(): array
    {
        try {
            $size = DB::table('jobs')->count();
            $failed = DB::table('failed_jobs')->count();
            return [true, "Pending: {$size}, Failed: {$failed}"];
        } catch (\Exception $e) {
            return [false, 'Failed: ' . $e->getMessage()];
        }
    }

    private function checkStorage(): array
    {
        $path = storage_path('app');
        $writable = is_writable($path);
        return [$writable, $writable ? 'Writable' : 'Not writable: ' . $path];
    }

    private function checkEnvironment(): array
    {
        $required = ['APP_KEY', 'DB_HOST', 'REDIS_HOST', 'MPESA_CONSUMER_KEY'];
        $missing = array_filter($required, fn($k) => empty(env($k)));
        if (empty($missing)) {
            return [true, 'All required env vars set'];
        }
        return [false, 'Missing: ' . implode(', ', $missing)];
    }
}
