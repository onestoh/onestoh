<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecureFileUpload
{
    private array $blocked = ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'exe', 'sh', 'bat', 'cmd', 'com', 'js', 'html', 'htm', 'xml', 'svg'];
    private int $maxSizeMb = 10;

    public function handle(Request $request, Closure $next)
    {
        foreach ($request->allFiles() as $file) {
            $files = is_array($file) ? $file : [$file];
            foreach ($files as $f) {
                if (!$f->isValid()) {
                    return response()->json(['message' => 'File upload error.'], 422);
                }

                if ($f->getSize() > $this->maxSizeMb * 1024 * 1024) {
                    return response()->json(['message' => "File exceeds maximum size of {$this->maxSizeMb}MB."], 422);
                }

                $ext = strtolower($f->getClientOriginalExtension());
                if (in_array($ext, $this->blocked, true)) {
                    Log::warning('Blocked file upload attempt', [
                        'extension' => $ext,
                        'filename' => $f->getClientOriginalName(),
                        'ip' => $request->ip(),
                        'user_id' => auth()->id(),
                    ]);
                    return response()->json(['message' => 'File type not allowed.'], 422);
                }

                // Check MIME type matches extension (prevent MIME spoofing)
                $detectedMime = $f->getMimeType();
                $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf', 'image/gif'];
                if (!in_array($detectedMime, $allowedMimes, true)) {
                    return response()->json(['message' => 'File type not permitted.'], 422);
                }
            }
        }

        return $next($request);
    }
}
