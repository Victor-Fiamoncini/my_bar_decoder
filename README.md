# My Bar Decoder 📄

Web application for decoding payment codes from PDF file using PHP and Laravel framework.

## Required Tools

- PHP v8.4.13
- Composer v2.8.12
- Laravel v12.43.1
- Livewire v3.7.3
- NodeJS v20.13.1

## Required PHP v8 Extensions

- pecl.php.net/imagick-3.8.1

## Imagick Setup

```bash
# Install ImageMagick system libraries first
sudo apt-get update
sudo apt-get install libmagickwand-dev imagemagick

# Install Imagick extension via PECL
pecl install imagick

# Find your PHP ini directory
php --ini

# Add the extension to your php.ini or create a new config file
echo "extension=imagick.so" >> $(php --ini | grep "Scan for additional" | awk '{print $NF}')/imagick.ini

# Check if the extension is loaded
php -m | grep imagick
```

----------
Released in 2025

By [Victor B. Fiamoncini](https://github.com/Victor-Fiamoncini) ☕️
