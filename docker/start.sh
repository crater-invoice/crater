#!/bin/bash

set -e

envsubst < .env.template  > .env.example

cp .env.example .env

sh /etc/wait-for $DB_HOST:$DB_PORT

php artisan key:generate --force
php artisan storage:link || true

if [ ${AUTO_INSTALL} == "true" ]
then
    php artisan migrate --force
    php artisan db:seed --force
    php artisan db:seed --class=DemoSeeder --force
fi

php-fpm
