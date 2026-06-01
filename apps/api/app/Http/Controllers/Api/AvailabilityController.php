<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AvailabilityController extends Controller
{
    public function __construct(private AvailabilityService $availability) {}

    public function calendar(Request $request, int $assetId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required|date|after_or_equal:today',
            'to'   => 'required|date|after:from|before_or_equal:' . now()->addMonths(6)->toDateString(),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $asset    = Asset::where('is_published', true)->findOrFail($assetId);
        $calendar = $this->availability->getCalendar(
            $asset,
            Carbon::parse($request->from),
            Carbon::parse($request->to)
        );

        return response()->json([
            'asset_id' => $assetId,
            'from'     => $request->from,
            'to'       => $request->to,
            'calendar' => $calendar,
        ]);
    }

    public function checkAvailability(Request $request, int $assetId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_at' => 'required|date|after:now',
            'end_at'   => 'required|date|after:start_at',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $asset     = Asset::where('is_published', true)->findOrFail($assetId);
        $available = $this->availability->isAvailable(
            $asset,
            Carbon::parse($request->start_at),
            Carbon::parse($request->end_at)
        );

        return response()->json(['available' => $available]);
    }

    public function blockDates(Request $request, int $assetId): JsonResponse
    {
        $asset = Asset::where('owner_id', $request->user()->id)->findOrFail($assetId);

        $validator = Validator::make($request->all(), [
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $this->availability->blockDates($asset, Carbon::parse($request->from), Carbon::parse($request->to));

        return response()->json(['message' => 'Dates blocked successfully.']);
    }

    public function unblockDates(Request $request, int $assetId): JsonResponse
    {
        $asset = Asset::where('owner_id', $request->user()->id)->findOrFail($assetId);

        $validator = Validator::make($request->all(), [
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $this->availability->unblockDates($asset, Carbon::parse($request->from), Carbon::parse($request->to));

        return response()->json(['message' => 'Dates unblocked successfully.']);
    }
}
