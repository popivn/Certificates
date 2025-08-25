# Sử dụng image PHP chính thức với phiên bản 8.2 và FPM
FROM php:8.2-fpm-bullseye

# Thiết lập thư mục làm việc mặc định trong container
WORKDIR /var/www/html

# Cài đặt các extension PHP cần thiết cho Laravel và các thư viện khác
# Lệnh 'apt-get' được sử dụng để cài đặt các gói hệ thống.
# Lệnh 'docker-php-ext-install' dùng để cài đặt các extension của PHP.
# Các extension này là bắt buộc để Laravel hoạt động.
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath zip

# Cài đặt Composer
# Sử dụng 'COPY --from=composer' để lấy file thực thi của Composer
# từ image chính thức của nó, giúp quá trình cài đặt nhanh chóng.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy các file Composer vào image. Đây là bước quan trọng để tận dụng cache của Docker.
# Nếu chỉ có các file này thay đổi, Docker sẽ chỉ chạy lại bước 'composer install'
# thay vì chạy lại toàn bộ từ đầu.
COPY composer.json composer.lock ./

# Chạy Composer để cài đặt các phụ thuộc.
# Lệnh này sẽ tải và cài đặt các thư viện cần thiết.
RUN composer install --no-dev --optimize-autoloader

# Copy toàn bộ source code của bạn vào image
# Sau khi các phụ thuộc đã được cài đặt, chúng ta mới copy toàn bộ code.
# Việc này đảm bảo nếu bạn chỉ thay đổi code mà không thay đổi file composer.json/lock,
# Docker sẽ sử dụng lại cache và không chạy lại 'composer install'.
COPY . .

# Phân quyền cho Laravel
# Chuyển quyền sở hữu thư mục project cho người dùng 'www-data' (người dùng mặc định của PHP FPM)
# và cấp quyền ghi cho các thư mục 'storage' và 'bootstrap/cache'.
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Chuyển sang người dùng 'www-data' để chạy các tiến trình PHP
# Điều này giúp tăng tính bảo mật bằng cách không chạy ứng dụng với quyền root.
USER www-data

# Mở cổng 9000, cổng mặc định của PHP-FPM
EXPOSE 9000

# Lệnh mặc định khi container chạy, khởi động PHP-FPM
CMD ["php-fpm"]
