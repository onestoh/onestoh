<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MultiCurrencyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MultiCurrencyController extends Controller
{
    public function __construct(private MultiCurrencyService $service) {}

    public function supported(): JsonResponse
    {
        return response()->json(['currencies' => $this->service->getSupportedCurrencies()]);
    }

    public function convert(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'from'   => 'required|string|size:3',
            'to'     => 'required|string|size:3',
        ]);

        $from      = strtoupper($request->from);
        $to        = strtoupper($request->to);
        $converted = $this->service->convert((float) $request->amount, $from, $to);

        return response()->json([
            'original'  => $request->amount,
            'from'      => $from,
            'to'        => $to,
            'converted' => $converted,
            'formatted' => $this->service->formatCurrency($converted, $to),
        ]);
    }

    public function setPreferred(Request $request): JsonResponse
    {
        $request->validate(['currency' => 'required|string|size:3|in:KES,UGX,TZS,USD,GBP,EUR']);
        $request->user()->update(['preferred_currency' => $request->currency]);
        return response()->json(['message' => 'Preferred currency updated']);
    }
}
