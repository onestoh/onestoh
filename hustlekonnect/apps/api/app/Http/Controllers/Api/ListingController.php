<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Yard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function assets(Request $request): JsonResponse
    {
        $request->validate([
            'country'    => 'nullable|string|size:2',
            'type'       => 'nullable|string',
            'min_price'  => 'nullable|numeric|min:0',
            'max_price'  => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after:start_date',
            'per_page'   => 'nullable|integer|min:1|max:100',
            'sort'       => 'nullable|in:price_asc,price_desc,rating,newest',
        ]);

        $query = Asset::with(['yard:id,name,city,country,rating', 'media' => fn($q) => $q->where('is_primary', true)])
            ->where('status', 'available')
            ->where('is_listed', true);

        if ($request->country)   $query->whereHas('yard', fn($q) => $q->where('country', $request->country));
        if ($request->type)      $query->where('asset_type', $request->type);
        if ($request->min_price) $query->where('daily_rate', '>=', $request->min_price);
        if ($request->max_price) $query->where('daily_rate', '<=', $request->max_price);

        if ($request->start_date && $request->end_date) {
            $query->whereDoesntHave('bookings', fn($q) =>
                $q->whereNotIn('status', ['cancelled', 'rejected'])
                  ->where(fn($inner) =>
                      $inner->whereBetween('start_date', [$request->start_date, $request->end_date])
                            ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                  )
            );
        }

        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'price_asc'  => $query->orderBy('daily_rate', 'asc'),
            'price_desc' => $query->orderBy('daily_rate', 'desc'),
            'rating'     => $query->orderBy('rating', 'desc'),
            default      => $query->orderBy('created_at', 'desc'),
        };

        $assets = $query->paginate($request->get('per_page', 20));
        return response()->json($assets);
    }

    public function asset(int $id): JsonResponse
    {
        $asset = Asset::with(['yard.user:id,name,rating,trust_score', 'media', 'reviews' => fn($q) => $q->latest()->limit(5)])
            ->where('is_listed', true)
            ->findOrFail($id);

        return response()->json(['data' => $asset]);
    }

    public function yards(Request $request): JsonResponse
    {
        $yards = Yard::with(['assets' => fn($q) => $q->where('is_listed', true)->limit(3)])
            ->where('is_active', true)
            ->when($request->country, fn($q) => $q->where('country', $request->country))
            ->orderBy('rating', 'desc')
            ->paginate(20);

        return response()->json($yards);
    }

    public function yard(int $id): JsonResponse
    {
        $yard = Yard::with(['assets' => fn($q) => $q->where('is_listed', true), 'user:id,name,trust_score'])
            ->where('is_active', true)
            ->findOrFail($id);

        return response()->json(['data' => $yard]);
    }
}
