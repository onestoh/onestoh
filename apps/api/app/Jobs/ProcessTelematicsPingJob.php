<?php
namespace App\Jobs;

use App\Models\TelematicsDevice;
use App\Services\TelematicsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\Log;

class ProcessTelematicsPingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(private array $pingData) {}

    public function handle(TelematicsService $service): void
    {
        try {
            $ping = $service->processPing($this->pingData);

            // If ignition just turned on, start a trip
            if (!empty($this->pingData['ignition_on'])) {
                $device = TelematicsDevice::where('device_id', $this->pingData['device_id'])->first();
                if ($device) {
                    $activeTrip = \App\Models\TelematicsTrip::where('asset_id', $device->asset_id)
                        ->whereNull('ended_at')
                        ->first();

                    if (!$activeTrip) {
                        $service->startTrip(
                            $this->pingData['device_id'],
                            $this->pingData['latitude'],
                            $this->pingData['longitude']
                        );
                    }
                }
            }

            // If ignition turned off, end active trip
            if (isset($this->pingData['ignition_on']) && !$this->pingData['ignition_on']) {
                $device = TelematicsDevice::where('device_id', $this->pingData['device_id'])->first();
                if ($device) {
                    $activeTrip = \App\Models\TelematicsTrip::where('asset_id', $device->asset_id)
                        ->whereNull('ended_at')
                        ->first();
                    if ($activeTrip) {
                        $service->endTrip($activeTrip, $this->pingData);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('ProcessTelematicsPingJob failed', [
                'device_id' => $this->pingData['device_id'] ?? 'unknown',
                'error'     => $e->getMessage(),
            ]);
            $this->fail($e);
        }
    }

    public function queue(): string
    {
        return 'telematics';
    }
}
