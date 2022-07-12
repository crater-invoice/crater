FROM php:7.4-fpm

ENV user=crater-user
ENV uid=1000

RUN apt-get update && apt-get install -y wait-for-it

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
    gettext-base

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

COPY . /var/www/
COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
RUN chown -R $user:$user /var/www/
RUN chmod -R 775 /var/www/storage
RUN chmod -R 775 /var/www/bootstrap

# Set working directory
WORKDIR /var/www

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

COPY docker/start.sh start.sh
RUN chmod 775 start.sh
RUN rm -rf /tmp/artifacts

USER $user

ENTRYPOINT [ "./start.sh" ]
