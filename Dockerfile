FROM php:8.2-fpm-bullseye

# Cài dependency cần thiết (bắt buộc + optional)
RUN set -eux; \
    apt-get update; \
    # Gói bắt buộc
    apt-get install -y --no-install-recommends \
        libzip-dev \
        libicu-dev \
        libxml2-dev \
        unzip; \
    # Gói optional (nếu lỗi thì bỏ qua)
    (apt-get install -y wkhtmltopdf || echo "Skip wkhtmltopdf"); \
    rm -rf /var/lib/apt/lists/*

# Cài extension PHP cần thiết
RUN docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        bcmath \
        zip \
        intl \
        opcache

WORKDIR /var/www/html

# Copy toàn bộ source vào container
COPY . .

# Phân quyền cho Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
