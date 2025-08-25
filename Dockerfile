##########
# Stage 1: Install PHP dependencies (no-dev) using Composer image
##########
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

##########
# Stage 2: Runtime image (PHP-FPM)
##########
FROM php:8.2-fpm-bullseye

# Install system packages and PHP extensions required by the app
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        unzip \
        libpng-dev \
        libzip-dev \
        libicu-dev \
        libxml2-dev \
        zlib1g-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        ca-certificates \
        curl \
        fonts-dejavu \
        fonts-noto \
        fonts-noto-cjk \
        wkhtmltopdf \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        zip \
        intl \
        opcache

WORKDIR /var/www/html

# Copy application source (context filtered by .dockerignore)
COPY . /var/www/html

# Copy vendor from the Composer stage
COPY --from=vendor /app/vendor /var/www/html/vendor

# Permissions for Laravel writable dirs
RUN chown -R www-data:www-data /var/www/html \
    && mkdir -p storage/framework/{cache,sessions,views} \
    && mkdir -p bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
