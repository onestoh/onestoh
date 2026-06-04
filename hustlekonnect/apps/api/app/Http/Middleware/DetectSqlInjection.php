<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DetectSqlInjection
{
    private array $patterns = [
        '/(\bUNION\b.*\bSELECT\b)/i', '/(\bDROP\b.*\bTABLE\b)/i',
        '/(\bINSERT\b.*\bINTO\b)/i', '/(\bDELETE\b.*\bFROM\b)/i',
        '/(\bSELECT\b.*\bFROM\b)/i', '/(\bEXEC\b|\bEXECUTE\b)/i',
        '/(--|;--|\bxp_)/i', '/(\bOR\b\s+[\'"]?\d+[\'"]?\s*=\s*[\'"]?\d+[\'"]?)/i',
        "/(\bOR\b\s+'[^']*'\s*=\s*'[^']*')/i", '/(\bAND\b\s+1\s*=\s*1)/i',
    ];

    public function handle(Request $request, Closure $next)
    {
        foreach ($this->flatten(array_merge($request->query->all(), $request->json()->all() ?: [])) as $v) {
            if (!is_string($v)) continue;
            foreach ($this->patterns as $p) {
                if (preg_match($p, $v)) {
                    Log::critical('SQL injection attempt', ['ip' => $request->ip(), 'url' => $request->fullUrl(), 'input' => substr($v, 0, 200)]);
                    return response()->json(['message' => 'Invalid input detected.'], 400);
                }
            }
        }
        return $next($request);
    }

    private function flatten(array $a, string $p = ''): array
    {
        $r = [];
        foreach ($a as $k => $v) {
            is_array($v) ? ($r = array_merge($r, $this->flatten($v, $p.$k.'.'))) : ($r[$p.$k] = $v);
        }
        return $r;
    }
}
