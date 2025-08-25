# syntax=docker/dockerfile:1

### STAGE 1: Build dependencies using Composer và Node (nếu có)
FROM composer:latest AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev --no-interaction --prefer-dist --optimize-autoloader

### STAGE 2: Optional – build frontend nếu bạn sử dụng npm/yarn (Ví dụ dùng Node)
# FROM node:16-alpine AS node-builder
# WORKDIR /app
# COPY package.json yarn.lock ./
# RUN yarn install --production
# COPY . .
# RUN yarn build

### STAGE 3: Final production image
FROM php:8.2-fpm-alpine AS production
RUN apk add --no-cache nginx supervisor

# Tối ưu PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath
# Copy composer từ stage vendor
COPY --from=vendor /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
# Copy source và thư viện
COPY --from=vendor /app /var/www/html
COPY . .

# Tối ưu Laravel
RUN composer install --no-dev --no-autoloader && \
    composer dump-autoload --optimize && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Set quyền phù hợp (tốt nhất không dùng www-data làm owner writable toàn bộ)
RUN chown -R root:root /var/www/html && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy config Nginx & Supervisor (giả sử có sẵn file cấu hình)
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 80
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
