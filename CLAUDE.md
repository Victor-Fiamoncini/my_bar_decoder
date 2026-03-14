# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Development
composer dev           # Start dev server, queue worker, logs, and Vite watcher concurrently
composer setup         # Initial setup: install deps, migrate, build assets

# Testing
php artisan test                                              # Run all tests
composer test                                                 # Clear config cache, then run all tests
php artisan test tests/Feature/DashboardTest.php             # Run a specific file
php artisan test --filter=testName                           # Run tests matching a name

# Code formatting (run before finalizing changes)
vendor/bin/pint --dirty

# Frontend
npm run dev            # Vite dev server
npm run build          # Build assets for production
```

## Architecture

This is a Laravel 12 + Livewire 3 app that extracts Brazilian payment codes from uploaded PDFs using Google Vision API for OCR.

### Clean Architecture under `App\Core\Modules\Documents\`

All domain logic lives inside a single module. The layers are:

- **Domain** (`Domain/`) — Pure PHP readonly classes with no framework dependencies.
  - `ValueObjects/PaymentCode/PaymentCode` — encapsulates regex extraction of two Brazilian payment code formats: DAS barcode (48 digits) and standard bill code (47 digits). Created via `PaymentCode::tryCreateFromText(string $text)`. Throws `ExtractCodeException` when no pattern matches.
  - `Entities/Document/Document` — the aggregate entity: holds `name`, `paymentCode` (VO), `createdAt`, and `ownerId`. Built by the service after successful extraction.
  - `Repositories/DocumentRepository` — domain interface with a single `save(Document $document): void` method.
  - `UseCases/ExtractPaymentCodeUseCase/ExtractPaymentCodeUseCase` — interface defining the `execute(Input): Output` contract consumed by the UI layer.

- **Application** (`Application/`) — Use case implementation and port interfaces.
  - `ExtractPaymentCodeService` implements `ExtractPaymentCodeUseCase`. Calls `FileTextExtractor`, creates `PaymentCode` then `Document`, persists via `DocumentRepository`, and returns `Output($document)`. Throws `FailedToExtractPaymentCodeException` when the extractor returns empty text.
  - `Adapter/FileTextExtractor` — interface for PDF-to-text extraction.
  - `Adapter/DocumentDAO` — application-layer interface for **read** queries (`listByOwnerId`). Kept separate from the domain repository, which handles writes only.

- **Infrastructure** (`Infrastructure/`) — Concrete implementations.
  - `EloquentDocumentRepository` — implements `DocumentRepository::save()`, mapping the `Document` entity to the Eloquent `Document` model.
  - `EloquentDocumentDAO` — implements `DocumentDAO::listByOwnerId()`, returning a `LengthAwarePaginator` for the dashboard listing.
  - `GoogleVisionFileTextExtractor` — converts the first PDF page to PNG via Ghostscript/`exec()`, base64-encodes it, POSTs to Google Vision API (`TEXT_DETECTION`), returns the raw text. Requires `gs` (Ghostscript) and the PHP `imagick` extension.

Bindings in `AppServiceProvider`:
- `ExtractPaymentCodeUseCase` → `ExtractPaymentCodeService` (singleton, receives `GoogleVisionFileTextExtractor` + `EloquentDocumentRepository`)
- `DocumentDAO` → `EloquentDocumentDAO` (bound for Dashboard reads)

### UI layer

`App\Livewire\Dashboard` is the sole component. `DocumentDAO` is injected via `boot(DocumentDAO $documentDAO)` (Livewire's lifecycle hook — runs on every request, not serialised). The `recentlyDocuments` computed property delegates to `$this->documentDAO->listByOwnerId(auth()->id(), 6)`.

On submit, `ExtractPaymentCodeUseCase::execute()` is called per file. Error handling distinguishes `FailedToExtractPaymentCodeException` (extraction-specific message) from all other `\Throwable`s (generic message); both append to a `$errors` array without stopping the loop.

Auth is handled by Laravel Fortify (with 2FA).

### Error handling flow

```
Dashboard::submit()
  → ExtractPaymentCodeService::execute()
      → FileTextExtractor::extractFromFilePath()   # throws \Exception on Vision API failure
      → (empty text) throws FailedToExtractPaymentCodeException
      → PaymentCode::tryCreateFromText()           # throws ExtractCodeException if no pattern matches
```

### Tests mirror the source structure

- `tests/Unit/Core/Modules/Documents/Domain/ValueObjects/PaymentCode/` — `PaymentCode` VO unit tests
- `tests/Unit/Core/Modules/Documents/Application/Services/ExtractPaymentCodeService/` — service unit tests (mocks `FileTextExtractor` + `DocumentRepository`)
- `tests/Feature/Core/Modules/Documents/Infrastructure/` — `GoogleVisionFileTextExtractor` integration tests (skipped without API key)
- `tests/Feature/DashboardTest.php` — Livewire feature tests; mocks `ExtractPaymentCodeUseCase`
- `tests/Fixtures/` — real PDF files used in integration tests

## Key conventions

- **Tests**: Written in Pest 4. Use `php artisan make:test --pest {Name}`. In `DashboardTest`, the domain `Document` entity and `App\Models\Document` (Eloquent) are both imported — alias the Eloquent one as `EloquentDocument` to avoid the name clash.
- **Mocking**: Use `$this->mock()` in feature tests and `Mockery::mock()` in unit tests — follow existing test conventions.
- **PHP style**: Constructor property promotion, explicit return types, readonly classes for DTOs, entities, and value objects. PHPDoc `@throws` blocks on methods that throw checked exceptions.
- **Livewire 3**: Inject dependencies that shouldn't be serialised via `boot()`, not `mount()`. Components live in `App\Livewire`.
- **Tailwind v4**: CSS-first config via `@theme` directive; use `@import "tailwindcss"` (not `@tailwind` directives). No `tailwind.config.js`.
- **Flux UI** (free tier): Prefer `<flux:*>` components when available over custom HTML.
- **Config**: Never call `env()` outside of config files. Google Vision API key is at `config('services.google_vision.api_key')`.
- **Database**: Prefer `Model::query()` over `DB::`. `Document` Eloquent model uses soft deletes.
- Do not create new top-level directories or change dependencies without approval.
