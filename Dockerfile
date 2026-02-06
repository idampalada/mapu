FROM php:8.1-apache

# -----------------------------
# System dependencies
# -----------------------------
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# -----------------------------
# PHP extensions
# -----------------------------
RUN docker-php-ext-install \
        zip \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

# -----------------------------
# Apache config (MPM FIX)
# -----------------------------
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork rewrite

# -----------------------------
# Working directory
# -----------------------------
WORKDIR /var/www/html

# -----------------------------
# Composer
# -----------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# -----------------------------
# App files
# -----------------------------
COPY --chown=www-data:www-data . .

# -----------------------------
# Permissions
# -----------------------------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 writable/

# -----------------------------
# Install PHP dependencies
# -----------------------------
USER www-data
RUN composer install --no-dev --optimize-autoloader

USER root

# -----------------------------
# Apache virtual host
# -----------------------------
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# -----------------------------
# PHP upload limits
# -----------------------------
RUN echo "upload_max_filesize=10M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=10M" >> /usr/local/etc/php/conf.d/uploads.ini
