#!/bin/sh

cd /var/www 

php artisan storage:link || true
php artisan key:generate
php artisan passport:keys || true