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

            // Onboarding steps
            $onboardingSteps = ['profile' => false, 'verification' => false, 'activity' => false];
            $onboardingComplete = false;
            if ($userId) {
                try {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        $onboardingSteps = [
                            'profile'      => (bool) $user->phone,
                            'verification' => \App\Models\Verification::where('user_id', $userId)->exists(),
                            'activity'     => \App\Models\Property::where('user_id', $userId)->exists() || \App\Models\Lease::where('tenant_id', $userId)->exists(),
                        ];
                        $onboardingComplete = array_sum($onboardingSteps) === 3;
                    }
                } catch (\Throwable $e) {}
            }
            $view->with('onboardingSteps', $onboardingSteps);
            $view->with('onboardingComplete', $onboardingComplete);
        });
    }
}
