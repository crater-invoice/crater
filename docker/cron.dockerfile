FROM php:8.1-fpm-bookworm

RUN apt update && apt install -y cron

RUN docker-php-ext-install pdo pdo_mysql bcmath

COPY docker/crontab /etc/crontabs/root
RUN crontab /etc/crontabs/root

CMD ["/usr/sbin/cron", "-f"]
