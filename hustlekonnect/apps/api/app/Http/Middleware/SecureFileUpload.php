<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecureFileUpload
{
    private array $blocked = ['php','phtml','php3','php4','php5','php7','exe','sh','bat','cmd','js','html','htm','svg','xml'];
    private array $allowedMimes = ['image/jpeg','image/png','image/webp','image/gif','application/pdf'];

    public function handle(Request $request, Closure $next)
    {
        foreach ($request->allFiles() as $file) {
            foreach (is_array($file) ? $file : [$file] as $f) {
                if (!$f->isValid()) return response()->json(['message' => 'File upload error.'], 422);
                if ($f->getSize() > 10 * 1024 * 1024) return response()->json(['message' => 'File too large. Maximum 10MB.'], 422);
                if (in_array(strtolower($f->getClientOriginalExtension()), $this->blocked, true)) {
                    Log::warning('Blocked file type', ['ext' => $f->getClientOriginalExtension(), 'ip' => $request->ip()]);
                    return response()->json(['message' => 'File type not allowed.'], 422);
                }
                if (!in_array($f->getMimeType(), $this->allowedMimes, true)) return response()->json(['message' => 'File type not permitted.'], 422);
            }
        }
        return $next($request);
    }
}
