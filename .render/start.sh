#!/bin/bash

php artisan migrate --force
php artisan livewire:publish --assets

php-fpm -D

nginx -g 'daemon off;'
