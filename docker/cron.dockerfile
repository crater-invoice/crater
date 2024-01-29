FROM php:8.1-fpm-alpine

RUN apk add --no-cache \
    php81-bcmath

RUN docker-php-ext-install pdo pdo_mysql bcmath

COPY /docker/crontab /etc/crontabs/root

CMD ["crond", "-f"]
