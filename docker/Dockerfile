FROM php:7.4-fpm-alpine

ENV user=crater-user
ENV uid=1000

# Install system dependencies
RUN apk update && apk add --no-cache --virtual \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    imagemagick \
    imagemagick-dev \
    mariadb-client \
    gettext

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN adduser -D -h /home/$user $user
RUN addgroup $user www-data
RUN addgroup $user root

RUN mkdir -p /home/$user/.composer 
RUN chown -R $user:$user /home/$user

COPY . /var/www/
RUN rm -rf /var/www/composer.lock
COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
RUN chown -R $user:$user /var/www/
RUN chmod -R 775 /var/www/storage
RUN chmod -R 775 /var/www/bootstrap

# Set working directory
WORKDIR /var/www

RUN curl -o wait-for.sh 'https://raw.githubusercontent.com/eficode/wait-for/v2.2.3/wait-for'
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
COPY docker/start.sh start.sh
RUN chmod +x start.sh
RUN chmod +x wait-for.sh
RUN rm -rf /tmp/artifacts

USER $user

ENTRYPOINT ["./start.sh"]
