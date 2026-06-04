<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    private array $except = ['password', 'password_confirmation', 'current_password'];

    public function handle(Request $request, Closure $next)
    {
        $request->merge($this->clean($request->all()));
        return $next($request);
    }

    private function clean(array $data): array
    {
        foreach ($data as $k => $v) {
            if (in_array($k, $this->except, true)) continue;
            $data[$k] = is_array($v) ? $this->clean($v) : (is_string($v) ? htmlspecialchars(strip_tags(trim($v)), ENT_QUOTES | ENT_HTML5, 'UTF-8', false) : $v);
        }
        return $data;
    }
}
