<?php

namespace HyperBill\HyperpayPtsp;

use Illuminate\Support\ServiceProvider;

class HyperpayPtspServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/hyperpay-ptsp.php', 'hyperpay-ptsp');

        // Register the service as a singleton
        $this->app->singleton('hyperpay-ptsp', function ($app) {
            return new HyperpayPtspService();
        });

        // Register the facade alias
        $this->app->alias('hyperpay-ptsp', HyperpayPtspService::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/hyperpay-ptsp.php' => config_path('hyperpay-ptsp.php'),
        ], 'hyperpay-ptsp-config');
    }
}

