# My Bar Decoder 🧾

- Web application for decoding payment codes from PDF files using PHP and Laravel framework with Livewire.
The main API used for decoding was [Google Vision API V1](https://www.google.com/aclk?sa=L&ai=DChsSEwiB_4jsquaRAxV1QEgAHfopFnwYACICCAEQABoCY2U&co=1&ase=2&gclid=CjwKCAiAjc7KBhBvEiwAE2BDOfO0O5OZHElPRG31iNloQbVU9HHBGc-RlrkW0LDc5Z60_qZd50JWAxoCUrQQAvD_BwE&cid=CAAS0wHkaPplRkGyi-pu_bPo0TJlkur-6F60aTymntTIX2Epe4zjkYhvQodBVYJm3QfWLXandJIeBlmkZO8_ZhwoPqWg3C61AAh10yJ3cbF8SdhSTG4ZhMD_TI5aiKGJp9FJMQ-M26Ff4bvOGsADFPfXgrcH6gLjg9_p9MxkGw9hEJo_wIl0grZYwZsOrgLGderttN35OFNO-ngGBIX70V2K-bqmK89q61-QqpREy8-Ct1HqxtXlFStCOjMzFPePoGO428k-US2TraQdPAyKCHmnpn2zb5KJ&cce=2&category=acrcp_v1_37&sig=AOD64_3VhbeZcYu09B3ML5Mrit9adXcumQ&q&nis=4&adurl&ved=2ahUKEwj5uYPsquaRAxXyBbkGHdvjJg0Q0Qx6BAgWEAE) which has a free plan.
- App designed to test the Cloud platform called [Render](https://render.com) and Laravel/Livewire FE library.
- The app uses [Resend](https://resend.com) to send emails without using SMTP protocol.
- The app uses [Cloudflare](https://www.cloudflare.com) services for DNS management.

## Required Tools

- PHP v8.4.13
- Composer v2.8.12
- Laravel v12.43.1
- Livewire v3.7.3
- NodeJS v20.13.1

## Required PHP v8 Extensions

- pecl.php.net/imagick-3.8.1

## Imagick Extension Setup (Debian)

```bash
# Install ImageMagick system libraries first
sudo apt-get update
sudo apt-get install libmagickwand-dev imagemagick ghostscript

# Install Imagick extension via PECL
pecl install imagick

# Find your PHP ini directory
php --ini

# Add the extension to your php.ini or create a new config file
echo "extension=imagick.so" >> $(php --ini | grep "Scan for additional" | awk '{print $NF}')/imagick.ini

# Check if the extension is loaded
php -m | grep imagick
```

## Development Setup (Using Laravel Sail)

```bash
cp .env.example .env

# Create PostgreSQL and Laravel containers
sail up -d

# Run database migrations
sail artisan migrate

# Run Vite dev server
sail npm run dev
```

## Architecture

The project follows a Clean Architecture approach with Domain-Driven Design (DDD) principles, separating business logic from framework dependencies and infrastructure concerns.

### Core Namespace Structure

The `\Core` namespace is organized into distinct layers:

```
App\Core\
├── 📦 Domain\          # Business logic and entities
├── ⚙️ Application\     # Use cases and services
└── 🔌 Infrastructure\  # External dependencies and implementations
```

### Domain Layer

Domain entities represent core business concepts with encapsulated business rules:
- Immutability: Entities use readonly properties to ensure state consistency
- Value Objects: Properties like `PaymentCode::$code` are protected to maintain invariants
- Business Rules: Validation and behavior are encapsulated within entities

Example: `PaymentCode` entity enforces payment code format rules and provides typed access to its properties.

### Application Layer

Application services/use-cases orchestrate domain entities and coordinate business workflows:
- Single Responsibility: Each service handles one specific use case
- Dependency Injection: Services depend on interfaces, not concrete implementations
- Framework Agnostic: No direct Laravel dependencies in service logic

### Infrastructure Layer

Concrete implementations of domain interfaces:
- Database interactions (Eloquent models/repositories)
- External API clients
- OCR services (Google Vision API)

----------
Released in 2025

By [Victor B. Fiamoncini](https://github.com/Victor-Fiamoncini) ☕️
