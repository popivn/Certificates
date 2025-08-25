# Sử dụng image PHP chính thức với phiên bản 8.2 và FPM, dựa trên hệ điều hành Bullseye
# FPM (FastCGI Process Manager) là một PHP SAPI (Server API) thường được dùng trong môi trường web server.
FROM php:8.2-fpm-bullseye

# Cài đặt các extension cần thiết cho Laravel và các thư viện khác
# Lệnh 'apt-get update' cập nhật danh sách gói.
# Lệnh 'apt-get install -y' cài đặt các gói cần thiết mà không cần xác nhận.
# Lưu ý: Các extension như pdo_mysql, mbstring, exif, pcntl là bắt buộc đối với hầu hết các ứng dụng Laravel.
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath zip

# Cài đặt Composer
# Composer là trình quản lý gói bắt buộc cho các dự án PHP, đặc biệt là Laravel.
# Sử dụng lệnh 'COPY --from=composer:latest' để copy Composer từ một image khác, đây là phương pháp hiệu quả và an toàn.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Đặt thư mục làm việc mặc định trong container
WORKDIR /var/www/html

# Copy toàn bộ code vào container, ngoại trừ các file trong .dockerignore
# .dockerignore giúp loại bỏ các file không cần thiết như thư mục 'vendor' hay 'node_modules',
# giúp giảm dung lượng image và tăng tốc độ build.
COPY . .

# Chạy Composer để cài đặt các phụ thuộc
# Lệnh này phải chạy sau khi code đã được copy để có file composer.json.
RUN composer install --no-dev --optimize-autoloader

# Phân quyền cho Laravel
# Chuyển quyền sở hữu thư mục project cho người dùng 'www-data' (người dùng mặc định của PHP FPM)
# và cấp quyền ghi cho các thư mục 'storage' và 'bootstrap/cache' để Laravel có thể hoạt động.
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Chuyển sang người dùng www-data
# Việc này đảm bảo các tiến trình PHP-FPM sẽ chạy với quyền hạn thấp nhất, tăng tính bảo mật.
USER www-data

# Mở cổng 9000, cổng mặc định của PHP-FPM
EXPOSE 9000

# Lệnh mặc định khi container chạy
CMD ["php-fpm"]