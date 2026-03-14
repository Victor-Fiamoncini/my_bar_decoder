<?php

namespace App\Providers;

use App\Core\Modules\Documents\Application\Adapter\DocumentDAO\DocumentDAO;
use App\Core\Modules\Documents\Application\Services\ExtractPaymentCodeService\ExtractPaymentCodeService;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\ExtractPaymentCodeUseCase;
use App\Core\Modules\Documents\Infrastructure\EloquentDocumentDAO;
use App\Core\Modules\Documents\Infrastructure\EloquentDocumentRepository;
use App\Core\Modules\Documents\Infrastructure\GoogleVisionFileTextExtractor;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DocumentDAO::class, EloquentDocumentDAO::class);

        $this->app->singleton(ExtractPaymentCodeUseCase::class, function ($app) {
            return new ExtractPaymentCodeService(
                fileTextExtractor: $app->make(GoogleVisionFileTextExtractor::class),
                documentRepository: $app->make(EloquentDocumentRepository::class)
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
