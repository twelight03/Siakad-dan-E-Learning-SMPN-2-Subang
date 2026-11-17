<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' || config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        // Anti-DDoS Global Web Rate Limiter (120 req/minute per IP)
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });

        // Anti-Brute Force & Login Rate Limiter (5 req/minute per IP)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Public Form Submissions & Registrations (15 req/minute per IP)
        RateLimiter::for('auth-action', function (Request $request) {
            return Limit::perMinute(15)->by($request->ip());
        });

        // Heavy Computational Actions (Reports & Auto-scheduling) (20 req/minute per IP)
        RateLimiter::for('heavy', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });
    }
}
