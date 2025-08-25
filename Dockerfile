FROM php:8.2-fpm-bullseye

# Cài extension Laravel thường cần
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
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo_mysql \
        mbstring \
        bcmath \
        zip \
        intl \
        opcache

WORKDIR /var/www/html

# Copy toàn bộ source code (bao gồm vendor đã cài ở local)
COPY . .

# Phân quyền cho Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
