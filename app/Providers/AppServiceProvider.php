<?php

namespace App\Providers;

use App\Core\Infra\GoogleVisionFileBarcodeExtractor;
use App\Core\Services\ExtractBarcodeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ExtractBarcodeService::class, function ($app) {
            return new ExtractBarcodeService($app->make(GoogleVisionFileBarcodeExtractor::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
