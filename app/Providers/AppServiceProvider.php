<?php

namespace App\Providers;

use App\Core\Data\Services\ExtractPaymentCode\ExtractPaymentCodeService;
use App\Core\Infra\EloquentDocumentDAO;
use App\Core\Infra\GoogleVisionFilePaymentCodeExtractor;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ExtractPaymentCodeService::class, function ($app) {
            return new ExtractPaymentCodeService(
                filePaymentCodeExtractor: $app->make(GoogleVisionFilePaymentCodeExtractor::class),
                documentDAO: $app->make(EloquentDocumentDAO::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
