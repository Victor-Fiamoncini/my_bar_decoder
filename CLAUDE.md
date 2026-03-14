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
  - `PaymentCode`: entity that encapsulates regex extraction of two Brazilian payment code formats — DAS barcode (48 digits) and standard bill code (47 digits). Created via `PaymentCode::tryCreateFromText()`.
  - `ExtractPaymentCodeUseCase`: interface defining the single `execute(Input): Output` contract used by the UI layer.
  - `ExtractCodeException`: thrown by `PaymentCode` when no valid code is found in the extracted text.

- **Application** (`Application/`) — Use case implementation and port interfaces.
  - `ExtractPaymentCodeService` implements `ExtractPaymentCodeUseCase`. Calls `FileTextExtractor` to get text from the PDF, creates a `PaymentCode` entity, persists via `DocumentDAO`, and returns an `Output`. Throws `FailedToExtractPaymentCodeException` when the extractor returns empty text.
  - `FileTextExtractor` and `DocumentDAO` are interfaces (ports) — the application layer never touches concrete implementations.

- **Infrastructure** (`Infrastructure/`) — Concrete implementations of the ports.
  - `GoogleVisionFileTextExtractor`: converts the first PDF page to PNG via Ghostscript/`exec()`, base64-encodes it, POSTs to Google Vision API (`TEXT_DETECTION`), returns the raw text. Requires `gs` (Ghostscript) and the PHP `imagick` extension.
  - `EloquentDocumentDAO`: persists extracted codes as `Document` Eloquent models.

Bindings are wired in `AppServiceProvider`: `ExtractPaymentCodeUseCase` is bound as a singleton to `ExtractPaymentCodeService` with its two dependencies injected.

### UI layer

`App\Livewire\Dashboard` is the sole component — handles PDF uploads (max 5 files, 5 MB each), calls `ExtractPaymentCodeUseCase::execute()` per file, and paginates `Document` results via a computed `recentlyDocuments` property. Error handling distinguishes `FailedToExtractPaymentCodeException` (extraction-specific message) from all other `\Throwable`s (generic message); both add to a `$errors` array without stopping the loop.

Auth is handled by Laravel Fortify (with 2FA).

### Error handling flow

```
Dashboard::submit()
  → ExtractPaymentCodeService::execute()
      → FileTextExtractor::extractFromFilePath()   # throws \Exception on Vision API failure
      → PaymentCode::tryCreateFromText()           # throws ExtractCodeException if no code found
      → (empty text) throws FailedToExtractPaymentCodeException
```

`FailedToExtractPaymentCodeException` is an application-layer exception (no code extracted).
`ExtractCodeException` is a domain-layer exception (text extracted but no valid code pattern matched).

## Key conventions

- **Tests**: Written in Pest 4. Unit tests go in `tests/Unit/`, feature tests in `tests/Feature/`. Use `php artisan make:test --pest {Name}`. Real PDF fixtures are in `tests/Fixtures/`.
- **Mocking**: Use `$this->mock()` in feature tests — see `DashboardTest.php` for the established pattern with `ExtractPaymentCodeUseCase`.
- **PHP style**: Constructor property promotion, explicit return types, readonly classes for DTOs and value objects. PHPDoc blocks for `@throws` annotations rather than inline comments.
- **Laravel 12 structure**: No `app/Http/Middleware/` dir — middleware registered in `bootstrap/app.php`.
- **Livewire 3**: Use `wire:model.live` for real-time binding, `$this->dispatch()` for events. Components live in `App\Livewire`.
- **Tailwind v4**: CSS-first config via `@theme` directive; use `@import "tailwindcss"` (not `@tailwind` directives). No `tailwind.config.js`.
- **Flux UI** (free tier): Prefer `<flux:*>` components when available over custom HTML.
- **Config**: Never call `env()` outside of config files. Google Vision API key is at `config('services.google_vision.api_key')`.
- **Database**: Prefer `Model::query()` over `DB::`, use eager loading to avoid N+1 queries. `Document` uses soft deletes.
- Do not create new top-level directories or change dependencies without approval.
