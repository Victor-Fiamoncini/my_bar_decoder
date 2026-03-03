# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Development
composer dev           # Start dev server, queue worker, logs, and Vite watcher concurrently
composer setup         # Initial setup: install deps, migrate, build assets

# Testing
php artisan test                                              # Run all tests
php artisan test tests/Feature/ExampleTest.php               # Run a specific file
php artisan test --filter=testName                           # Run tests matching a name

# Code formatting (run before finalizing changes)
vendor/bin/pint --dirty

# Frontend
npm run dev            # Vite dev server
npm run build          # Build assets for production
```

## Architecture

This is a Laravel 12 + Livewire 3 app that extracts Brazilian payment codes from uploaded PDFs using Google Vision API for OCR.

### Clean Architecture layers under `App\Core\`

- **Domain** (`App\Core\Domain\`) — Business entities with readonly properties; no framework dependencies. `PaymentCode` encapsulates regex extraction of two Brazilian payment code formats: DAS barcode (48 digits) and standard bill code (47 digits).
- **Application** (`App\Core\Application\`) — Use cases (e.g., `ExtractPaymentCodeService`) that depend on interfaces, not concrete implementations.
- **Infrastructure** (`App\Core\Infrastructure\`) — Concrete implementations:
  - `GoogleVisionFileTextExtractor`: converts PDF pages to PNG via Ghostscript/Imagick, sends base64 images to Google Vision API, extracts `TEXT_DETECTION` results.
  - `EloquentDocumentDAO`: persists extracted codes as `Document` Eloquent models.

Service bindings are wired in a service provider. `ExtractPaymentCodeService` is registered as a singleton.

### UI layer

`App\Livewire\Dashboard` is the main component — handles PDF uploads (max 10 files, 5 MB each), triggers extraction, and paginates `Document` results. Auth is handled by Laravel Fortify (with 2FA).

### PDF processing dependency

Ghostscript and the PHP `imagick` extension must be installed locally for PDF-to-PNG conversion to work. See README.md for setup instructions.

## Key conventions

- **Tests**: Written in Pest 4. Unit tests go in `tests/Unit/`, feature tests in `tests/Feature/`, browser tests in `tests/Browser/`. Use `php artisan make:test --pest {Name}`.
- **Mocking**: Use `Pest\Laravel\mock` (imported via `use function Pest\Laravel\mock;`) or `$this->mock()` — follow existing test conventions.
- **PHP style**: Constructor property promotion, explicit return types, PHPDoc blocks instead of inline comments. Enums use TitleCase keys.
- **Laravel 12 structure**: No `app/Http/Middleware/` dir — middleware registered in `bootstrap/app.php`. Commands in `app/Console/Commands/` auto-register.
- **Livewire 3**: Use `wire:model.live` for real-time binding, `$this->dispatch()` for events. Components live in `App\Livewire` namespace.
- **Tailwind v4**: CSS-first config via `@theme` directive; use `@import "tailwindcss"` (not `@tailwind` directives). No `tailwind.config.js`.
- **Flux UI** (free tier): Prefer `<flux:*>` components when available over custom HTML.
- **Config**: Never call `env()` outside of config files; always use `config('key')`.
- **Database**: Prefer `Model::query()` over `DB::`, use eager loading to avoid N+1 queries.
- Do not create new top-level directories or change dependencies without approval.
