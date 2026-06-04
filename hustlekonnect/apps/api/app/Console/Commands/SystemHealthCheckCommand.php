<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SystemHealthCheckCommand extends Command
{
    protected $signature   = 'system:health-check';
    protected $description = 'Run HustleKonnect system health check';

    public function handle(): int
    {
        $this->info('🔍 HustleKonnect System Health Check');
        $this->newLine();
        $allOk = true;

        $checks = [
            'MySQL Database' => fn() => [true, 'Users: ' . DB::table('users')->count()],
            'Redis Cache'    => function () {
                Cache::put('hc', 'ok', 10);
                return [Cache::get('hc') === 'ok', 'Read/write OK'];
            },
            'Queue Jobs'     => fn() => [true, 'Pending: ' . DB::table('jobs')->count() . ', Failed: ' . DB::table('failed_jobs')->count()],
            'Storage'        => fn() => [is_writable(storage_path('app')), is_writable(storage_path('app')) ? 'Writable' : 'NOT writable'],
            'Environment'    => function () {
                $missing = array_filter(['APP_KEY','DB_HOST','REDIS_HOST'], fn($k) => empty(env($k)));
                return [empty($missing), empty($missing) ? 'All required vars set' : 'Missing: '.implode(', ', $missing)];
            },
        ];

        foreach ($checks as $name => $fn) {
            try {
                [$ok, $detail] = $fn();
                $this->line(($ok ? '✅' : '❌') . " {$name}: {$detail}");
                if (!$ok) $allOk = false;
            } catch (\Exception $e) {
                $this->line("❌ {$name}: " . $e->getMessage());
                $allOk = false;
            }
        }

        $this->newLine();
        if ($allOk) { $this->info('✅ All systems operational'); return Command::SUCCESS; }
        $this->error('❌ Some systems need attention'); return Command::FAILURE;
    }
}
