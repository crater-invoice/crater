FROM php:8.1-fpm as build

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libmagickwand-dev \
    mariadb-client

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u 1000 -d /home/crater-user crater-user
RUN mkdir -p /home/crater-user/.composer && \
    chown -R crater-user:crater-user /home/crater-user

# Mounted volumes
COPY ./ /var/www
COPY ./docker-compose/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
COPY ./uffizzi/.env.example /var/www/.env

# Set working directory
WORKDIR /var/www

RUN chown -R crater-user:crater-user ./
RUN chmod -R 775 composer.json composer.lock \ 
        composer.lock storage/framework/ \
        storage/logs/ bootstrap/cache/ /home/crater-user/.composer

RUN composer config --no-plugins allow-plugins.pestphp/pest-plugin true && \
    composer install --no-interaction --prefer-dist --optimize-autoloader && \
    php artisan storage:link || true && \
    php artisan key:generate

FROM php:8.0-fpm-alpine

RUN apk add --no-cache \
    php8-bcmath

RUN docker-php-ext-install pdo pdo_mysql bcmath

COPY docker-compose/crontab /etc/crontabs/root

# Mounted volumes
COPY --from=build /var/www /var/www

RUN chown -R $(whoami):$(whoami) /var/www/
RUN chmod -R 775 /var/www/
RUN chown -R $(whoami):$(whoami) /var/log/
RUN chmod -R 775 /var/log/

CMD ["crond", "-f"]
