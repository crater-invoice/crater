FROM php:8.0-fpm-alpine

RUN apk add --no-cache \
    php8-bcmath

RUN docker-php-ext-install pdo pdo_mysql bcmath

COPY docker-compose/crontab /etc/crontabs/root

CMD ["crond", "-f"]
