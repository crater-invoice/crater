FROM php:8.1-fpm

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
    mariadb-client \
    npm

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
RUN chown -R $(whoami):$(whoami) /var/log/
RUN chmod -R 775 /var/log

# Cleanup manually generated build files
RUN rm -rf /var/www/public/build
RUN npm config set user 0
RUN npm config set unsafe-perm true
# Frontend bulding
RUN sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/g' /var/www/.env
RUN sed -i 's/DB_DATABASE=crater/DB_DATABASE=\/tmp\/crater.sqlite/g' /var/www/.env
RUN touch /tmp/crater.sqlite
RUN composer install --no-interaction --prefer-dist
RUN npm i -f
RUN npm install --save-dev sass
RUN export NODE_OPTIONS="--max-old-space-size=4096" && /usr/bin/npx vite build --target=es2020
RUN sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/g' /var/www/.env
RUN sed -i 's/DB_DATABASE=\/tmp\/crater.sqlite/DB_DATABASE=crater/g' /var/www/.env

USER crater-user
