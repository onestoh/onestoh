<?php

namespace App\Providers;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Inject unread notification count into every dashboard view
        View::composer('layouts.dashboard', function ($view) {
            $userId = session('user_id');
            $unreadNotifCount = 0;

            if ($userId) {
                try {
                    $unreadNotifCount = NotificationLog::where('user_id', $userId)
                        ->where('is_read', false)
                        ->count();
                } catch (\Throwable $e) {
                    // Silently fail if table isn't available
                }
            }

            $view->with('unreadNotifCount', $unreadNotifCount);
        });
    }
}
