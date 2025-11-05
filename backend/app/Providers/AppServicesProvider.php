<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServicesProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Services\Contracts\IContactService::class,
            \App\Services\ContactService::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
