<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DetectSqlInjection
{
    private array $patterns = [
        '/(\bUNION\b.*\bSELECT\b)/i',
        '/(\bDROP\b.*\bTABLE\b)/i',
        '/(\bINSERT\b.*\bINTO\b)/i',
        '/(\bDELETE\b.*\bFROM\b)/i',
        '/(\bSELECT\b.*\bFROM\b)/i',
        '/(\bEXEC\b|\bEXECUTE\b)/i',
        '/(--|\bxp_|\bsp_)/i',
        '/(\bOR\b\s+[\'"\]?\d+[\'"\]?\s*=\s*[\'"\]?\d+[\'"\]?)/i',
        '/(\bAND\b\s+[\'"\]?\d+[\'"\]?\s*=\s*[\'"\]?\d+[\'"\]?)/i',
        "/(\bOR\b\s+'[^']*'\s*=\s*'[^']*')/i",
    ];

    public function handle(Request $request, Closure $next)
    {
        $inputs = array_merge(
            $request->query->all(),
            is_array($request->json()->all()) ? $request->json()->all() : []
        );

        foreach ($this->flattenArray($inputs) as $value) {
            if (!is_string($value)) continue;
            foreach ($this->patterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    Log::critical('SQL injection attempt detected', [
                        'ip' => $request->ip(),
                        'url' => $request->fullUrl(),
                        'input' => substr($value, 0, 200),
                        'user_id' => auth()->id(),
                    ]);
                    return response()->json(['message' => 'Invalid input detected.'], 400);
                }
            }
        }

        return $next($request);
    }

    private function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $prefix . $key . '.'));
            } else {
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }
}
