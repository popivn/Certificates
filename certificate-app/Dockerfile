FROM php:8.2-fpm-bullseye

# Install system dependencies and PHP extensions commonly needed by Laravel
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        libicu-dev \
        libxml2-dev \
        zlib1g-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        locales \
        gnupg \
        ca-certificates \
        curl \
        fonts-dejavu \
        fonts-liberation \
        fonts-noto \
        fonts-noto-cjk \
        fonts-noto-color-emoji \
        wkhtmltopdf \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
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

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application source
COPY . /var/www/html

# Ensure storage and bootstrap cache are writable
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && find /var/www/html/storage -type d -exec chmod 775 {} \; \
    && chmod -R 775 /var/www/html/bootstrap/cache

USER www-data

EXPOSE 9000

CMD ["php-fpm"]


