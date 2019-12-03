FROM php:7.2-fpm-alpine

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN apk add --no-cache curl git tar unzip libpng-dev libxml2-dev

RUN docker-php-ext-install bcmath ctype json gd mbstring pdo pdo_mysql tokenizer xml && \
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

