<?php

namespace App\Providers;

use App\Services\PaymentProcessors\AsaasPaymentProcessor;
use App\Services\PaymentProcessors\PaymentProcessor;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            PaymentProcessor::class,
            AsaasPaymentProcessor::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
