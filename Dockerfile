# Stage 1: Build Composer dependencies
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Stage 2: Build Node assets (nếu bạn dùng vite/mix)
FROM node:20 AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 3: PHP-FPM + Nginx (production)
FROM php:8.2-fpm-alpine

# Cài các extension Laravel cần
RUN apk add --no-cache \
    bash git curl unzip supervisor nginx icu-dev libpng-dev libjpeg-turbo-dev libzip-dev oniguruma-dev \
    && docker-php-ext-install intl pdo pdo_mysql gd zip mbstring opcache \
    && rm -rf /var/cache/apk/*

# Tạo user không chạy bằng root
RUN addgroup -g 1000 laravel && adduser -G laravel -g laravel -s /bin/sh -D laravel

WORKDIR /var/www/html

# Copy source code
COPY . .
# Copy vendor từ stage composer
COPY --from=vendor /app/vendor ./vendor
# Copy build assets (vite/mix)
COPY --from=frontend /app/public/build ./public/build

# Copy config nginx và supervisor
COPY ./nginx.conf /etc/nginx/http.d/default.conf
COPY ./supervisor.conf /etc/supervisord.conf

# Phân quyền
RUN chown -R laravel:laravel /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

USER laravel

EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
