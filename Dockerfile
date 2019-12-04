FROM composer as composer

# Copy composer files from project root into composer container's working dir
COPY composer.* /app/
 
# Run composer to build dependencies in vendor folder
RUN set -xe \
  && composer install --no-dev --no-scripts --no-suggest --no-interaction --prefer-dist --optimize-autoloader
   
# Copy everything from project root into composer container's working dir
COPY . /app
    
# Generated optimized autoload files containing all classes from vendor folder and project itself
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

FROM php:7.4.0-fpm-alpine

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN apk add --no-cache libpng-dev libxml2-dev && \
    docker-php-ext-install bcmath ctype json gd mbstring pdo pdo_mysql tokenizer xml

# Set container's working dir
WORKDIR /app
 
# Copy everything from project root into php container's working dir
COPY . /app

# Copy vendor folder from composer container into php container
COPY --from=composer /app/vendor /app/vendor

RUN php artisan config:cache && \
    chmod -R 755 storage bootstrap/cache && \
    chown -R www-data:www-data storage

EXPOSE 9000
CMD ["php-fpm", "--nodaemonize"]

