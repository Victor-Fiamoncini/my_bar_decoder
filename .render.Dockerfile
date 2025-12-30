# Stage 1: Build front-end assets
FROM node:20-alpine AS frontend-builder

WORKDIR /app

# Copy package files first
COPY package*.json ./

# Install dependencies
RUN npm ci

# Copy only necessary files for build (avoid copying everything)
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

# Run build
RUN npm run build

# Stage 2: PHP application
FROM php:8.4-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    libmagickwand-dev \
    imagemagick \
    nginx \
    ghostscript \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Install Imagick via PECL
RUN pecl install imagick-3.8.1 \
    && docker-php-ext-enable imagick

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Copy built front-end assets from frontend-builder stage
COPY --from=frontend-builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Configure Nginx
COPY .render/nginx.conf /etc/nginx/sites-available/default

# Expose port
EXPOSE 8080

# Start script
COPY .render/start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
