<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();
        Schema::defaultStringLength(191);
        $this->app->bind('settings', function () {
            return Cache::rememberForever('settings', function () {
                return Setting::select('key', 'value')
                    ->get()
                    ->map(function ($i) {
                        return [
                            $i->key => $i->value
                        ];
                    })
                    ->collapse()
                    ->toArray();
            });
        });
    }
}
