#!/bin/sh

chmod 775 /var/www/ -R
chown 1000:33 /var/www -R

if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "created .env from .env.example"
fi

composer install --no-interaction --prefer-dist --optimize-autoloader

php artisan storage:link || true
php artisan key:generate

php-fpm
