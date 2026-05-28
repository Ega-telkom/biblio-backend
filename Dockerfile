FROM php:8.5-fpm-alpine

# 1. Install utility dasar yang dibutuhkan runtime aplikasi
RUN apk add --no-cache git curl unzip gnu-libiconv

# 2. Ambil script installer resmi dari repository terpercaya
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# 3. FIX MUTLAK: Biarkan script yang merakit semua ekstensi PHP 8.5 secara otomatis & aman
RUN install-php-extensions \
    gd \
    pdo \
    imagick \
    pdo_pgsql \
    pgsql \
    intl \
    zip \
    bcmath \
    exif \
    opcache \
    redis

# ---- Sisa konfigurasi Laravel ----
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

RUN echo "upload_max_filesize = 100M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini

EXPOSE 9000
CMD ["php-fpm"]
