<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppRepositoriesProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\IContactRepository::class,
            \App\Repositories\ContactRepository::class
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
