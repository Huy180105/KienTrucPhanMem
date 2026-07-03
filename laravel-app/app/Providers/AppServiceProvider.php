<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\HopDong;
use App\Observers\HopDongObserver;

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
        HopDong::observe(HopDongObserver::class);

        if (request()->header('X-Forwarded-Proto') === 'https' || str_contains(request()->header('Host', ''), 'ngrok-free')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
