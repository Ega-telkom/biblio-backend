FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    postgresql-dev \
    libpng-dev \
    libzip-dev \
    libjpeg-turbo-dev \
    icu-dev \
    autoconf g++ make \
    zip unzip git curl \
    gnu-libiconv

RUN docker-php-ext-configure gd --with-jpeg && \
    docker-php-ext-install \
    pdo pdo_pgsql pgsql gd zip bcmath exif intl opcache

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]