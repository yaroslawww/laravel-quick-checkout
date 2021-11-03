<?php

namespace QuickCheckout;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/quick-checkout.php' => config_path('quick-checkout.php'),
            ], 'config');

            $this->commands([
                //
            ]);
        }

        $this->app->bind('quick-checkout', function ($app) {
            return new CheckoutManager($app);
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/quick-checkout.php', 'quick-checkout');
    }
}
