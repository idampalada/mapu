FROM php:8.1-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        zip \
        intl \
        mbstring \
        xml \
        gd \
        opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Simpan dulu folder vendor/myth/auth yang sudah dimodifikasi manual
# (ikut ter-copy karena dikecualikan di .dockerignore)
COPY vendor/myth/auth /tmp/myth-auth-custom

# Copy seluruh app source (vendor lain tidak ikut, sesuai .dockerignore)
COPY . .

# Install vendor lain via composer seperti biasa
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Timpa balik vendor/myth/auth dengan versi custom yang sudah dimodifikasi
RUN rm -rf /var/www/html/vendor/myth/auth \
    && cp -r /tmp/myth-auth-custom /var/www/html/vendor/myth/auth \
    && rm -rf /tmp/myth-auth-custom

# Permissions untuk folder writable (log, cache, session, uploads)
RUN chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable

EXPOSE 9000

CMD ["php-fpm"]