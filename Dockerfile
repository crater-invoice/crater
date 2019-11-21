FROM php:7.2-fpm-alpine

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN apk add --no-cache curl git tar unzip libpng-dev libxml2-dev

RUN docker-php-ext-install bcmath && \
    docker-php-ext-install ctype && \
    docker-php-ext-install json && \
    docker-php-ext-install gd && \
    docker-php-ext-install mbstring && \
    docker-php-ext-install pdo && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install tokenizer && \
    docker-php-ext-install xml && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer --version

WORKDIR /var/www

COPY . /var/www

RUN composer install --optimize-autoloader && \
    php artisan config:cache && \
    chmod -R 755 storage bootstrap/cache && \
    chown -R www-data:www-data storage

EXPOSE 9000
CMD ["php-fpm"]

