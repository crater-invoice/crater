#!/bin/sh

if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "created .env from .env.example"
fi

composer install --no-interaction --prefer-dist --optimize-autoloader

php artisan storage:link || true
php artisan key:generate

php-fpm
