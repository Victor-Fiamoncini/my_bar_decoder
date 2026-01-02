# My Bar Decoder

- Web application for decoding payment codes from PDF files using PHP and Laravel framework with Livewire.
The main API used for decoding was [Google Vision API V1](https://www.google.com/aclk?sa=L&ai=DChsSEwiB_4jsquaRAxV1QEgAHfopFnwYACICCAEQABoCY2U&co=1&ase=2&gclid=CjwKCAiAjc7KBhBvEiwAE2BDOfO0O5OZHElPRG31iNloQbVU9HHBGc-RlrkW0LDc5Z60_qZd50JWAxoCUrQQAvD_BwE&cid=CAAS0wHkaPplRkGyi-pu_bPo0TJlkur-6F60aTymntTIX2Epe4zjkYhvQodBVYJm3QfWLXandJIeBlmkZO8_ZhwoPqWg3C61AAh10yJ3cbF8SdhSTG4ZhMD_TI5aiKGJp9FJMQ-M26Ff4bvOGsADFPfXgrcH6gLjg9_p9MxkGw9hEJo_wIl0grZYwZsOrgLGderttN35OFNO-ngGBIX70V2K-bqmK89q61-QqpREy8-Ct1HqxtXlFStCOjMzFPePoGO428k-US2TraQdPAyKCHmnpn2zb5KJ&cce=2&category=acrcp_v1_37&sig=AOD64_3VhbeZcYu09B3ML5Mrit9adXcumQ&q&nis=4&adurl&ved=2ahUKEwj5uYPsquaRAxXyBbkGHdvjJg0Q0Qx6BAgWEAE) which has a free plan.
- App designed to test the Cloud platform called [Render](https://render.com) and Livewire FE library.
- The app also uses [Resend](https://resend.com) to send emails without using SMTP protocol.

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

----------
Released in 2025

By [Victor B. Fiamoncini](https://github.com/Victor-Fiamoncini) ☕️
