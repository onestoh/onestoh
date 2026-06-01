<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    private array $except = ['password', 'password_confirmation', 'current_password', 'new_password'];

    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        $cleaned = $this->cleanArray($input);
        $request->merge($cleaned);
        return $next($request);
    }

    private function cleanArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->except, true)) {
                continue;
            }
            if (is_array($value)) {
                $data[$key] = $this->cleanArray($value);
            } elseif (is_string($value)) {
                $data[$key] = $this->clean($value);
            }
        }
        return $data;
    }

    private function clean(string $value): string
    {
        $value = trim($value);
        $value = strip_tags($value);
        // Encode script injection attempts
        $value = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $value);
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
    }
}
