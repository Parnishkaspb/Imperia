<?php

namespace App\Providers;

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
        //
    }

    protected $policies = [
        \App\Models\Order::class => \App\Policies\OrderPolicy::class,
        \App\Models\OrderProduct::class => \App\Policies\OrderProductPolicy::class,
    ];
}
