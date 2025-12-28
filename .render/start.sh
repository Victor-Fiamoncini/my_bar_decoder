#!/bin/bash

php artisan config:clear
php artisan cache:clear
php artisan view:clear

php artisan livewire:publish --assets
php artisan vendor:publish --tag=flux-assets --force

php artisan migrate --force

php-fpm -D

nginx -g 'daemon off;'
