FROM php:7.4-fpm-alpine

WORKDIR /var/www

RUN apk add --no-cache \
            $PHPIZE_DEPS \
            freetype-dev \
            git \
            zip \
            libzip-dev \
            php7-bcmath \
            curl \
            unzip \
            libjpeg-turbo-dev \
            libpng-dev \
            libxml2-dev \
            mariadb-client \
            sqlite \
            php7-json \
            php7-openssl \
            php7-pdo \
            php7-pdo_mysql \
            php7-session \
            php7-simplexml \
            php7-tokenizer \
            php7-xml \
            imagemagick \
            imagemagick-libs \
            imagemagick-dev \
            php7-imagick \
            php7-pcntl \
            --repository http://dl-cdn.alpinelinux.org/alpine/v3.13/community/ gnu-libiconv=1.15-r3

ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php

RUN printf "\n" | pecl install \
		imagick && \
		docker-php-ext-enable --ini-name 20-imagick.ini imagick

RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip
RUN docker-php-ext-install iconv pdo pdo_mysql bcmath pcntl exif
RUN docker-php-ext-configure gd --with-jpeg --with-freetype
RUN docker-php-ext-install gd
