<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Yard;
use App\Services\DriverPoolService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    public function __construct(private DriverPoolService $driverPoolService) {}

    /** GET /drivers/available?yard_id=&date=YYYY-MM-DD&licence_class= */
    public function available(Request $request): JsonResponse
    {
        $data = $request->validate([
            'yard_id'       => 'required|exists:yards,id',
            'date'          => 'required|date',
            'licence_class' => 'nullable|string|max:5',
        ]);

        $yard     = Yard::findOrFail($data['yard_id']);
        $date     = Carbon::parse($data['date']);
        $licence  = $data['licence_class'] ?? 'B';

        $drivers = $this->driverPoolService->getAvailableDrivers($yard, $date, $licence);
        return response()->json($drivers);
    }

    /** POST /drivers/availability */
    public function setAvailability(Request $request): JsonResponse
    {
        $data = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'date'      => 'required|date',
            'status'    => 'required|in:available,unavailable,on_leave',
        ]);

        $driver = Driver::findOrFail($data['driver_id']);
        $this->driverPoolService->setDriverAvailability($driver, $data['date'], $data['status']);

        return response()->json(['message' => 'Availability updated.']);
    }

    /** GET /drivers/{driver}/performance */
    public function performance(Driver $driver): JsonResponse
    {
        $stats = $this->driverPoolService->getDriverPerformance($driver);
        return response()->json($stats);
    }
}
