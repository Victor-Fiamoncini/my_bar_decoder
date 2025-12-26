# Start from the Laravel Cloud PHP image
FROM laravelphp/cloud:php84

# Install ghostscript
RUN apk update && \
    apk add --no-cache ghostscript

# Optional: install additional tools like Imagick
# RUN apk add --no-cache imagemagick ghostscript

# Copy application code
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel Cloud needs a webroot user
CMD ["php-fpm"]
