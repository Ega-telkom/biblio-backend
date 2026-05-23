FROM php:8.5-fpm-alpine

# 1. Install runtime dependencies (library yang wajib menetap di container)
RUN apk add --no-cache \
    libpng \
    libjpeg-turbo \
    freetype \
    libzip \
    icu-libs \
    libpq \
    git \
    curl \
    unzip \
    gnu-libiconv

# 2. Install build dependencies (library yang hanya dipakai saat compile, lalu dihapus)
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    linux-headers

# 3. Compile GD secara terpisah (Wajib jalankan docker-php-ext-enable di PHP 8.5)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd

# 4. Compile ekstensi database & core (Dipecah agar aman dari perubahan API PHP 8.5)
RUN docker-php-ext-install -j$(nproc) pdo pdo_pgsql pgsql
RUN docker-php-ext-install -j$(nproc) zip bcmath exif intl opcache

# 5. Install Redis lewat PECL (Menggunakan versi terbaru agar kompatibel dengan PHP 8.5)
RUN pecl install redis && docker-php-ext-enable redis

# 6. Bersihkan build-dependencies untuk menghemat ruang penyimpanan
RUN apk del .build-deps

# ---- Sisa perintah setup Laravel / PHP kamu ----
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
