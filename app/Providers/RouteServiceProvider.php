<?php

namespace App\Providers;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('web', function (Request $request) {
            return [
                Limit::perMinute(600)->by($request->ip()),
            ];
        });

        RateLimiter::for('heavy', function (Request $request) {
            return [
                Limit::perMinute(2000)->by($request->ip()),
            ];
        });

        $this->routes(function () {
            Route::middleware(['web', 'throttle:web'])
                ->group(base_path('routes/web.php'));
        });
    }
}
