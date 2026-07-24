# ============================================================================
#  FILE BARU — milik aplikasi API (mapu/api-kendaraan/)
#  Letakkan : mapu/api-kendaraan/Dockerfile
#  Image PHP-FPM untuk aplikasi API CodeIgniter 4.
# ============================================================================

FROM php:8.2-fpm

# Dependensi sistem
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# Ekstensi PHP yang dibutuhkan CI4 + PostgreSQL
RUN docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        zip \
        intl \
        mbstring \
        xml \
        opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy source aplikasi API
COPY . .

# Install dependency
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Folder writable
RUN mkdir -p \
    /var/www/html/writable/cache \
    /var/www/html/writable/logs \
    /var/www/html/writable/session \
    && chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable

# Izinkan PHP-FPM membaca environment variables Docker
RUN echo "clear_env = no" >> /usr/local/etc/php-fpm.d/www.conf

EXPOSE 9000

CMD ["php-fpm"]
