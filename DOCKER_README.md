# Docker Setup cho Certificate App

## Cấu hình hiện tại

Dự án đã được cấu hình với Docker để chạy:
- **PHP 8.2-FPM** với Laravel
- **Nginx** làm web server
- **MySQL 8.0** làm database

## Cách sử dụng

### 1. Khởi động ứng dụng
```bash
# Build và khởi động tất cả services
docker-compose up -d --build

# Xem logs
docker-compose logs -f
```

### 2. Truy cập ứng dụng
- **Web app**: http://localhost:8080
- **Database**: localhost:3307 (user: laravel, password: laravel)

### 3. Quá trình khởi tạo tự động
Ứng dụng sẽ tự động:
- Chờ database sẵn sàng
- Tạo file `.env` nếu chưa có
- Generate `APP_KEY`
- Chạy migrations
- Tạo storage link
- Cache configuration

### 4. Cài đặt dependencies (nếu cần)
```bash
# Vào container PHP
docker-compose exec app bash

# Cài đặt Composer packages
composer install

# Cài đặt NPM packages (nếu có)
npm install
```

### 5. Quản lý services
```bash
# Dừng tất cả services
docker-compose down

# Dừng và xóa volumes
docker-compose down -v

# Restart service cụ thể
docker-compose restart app

# Xem status
docker-compose ps

# Xem health status
docker-compose ps --format "table {{.Name}}\t{{.Status}}\t{{.Health}}"
```

## Cấu hình

### Environment Variables
Tạo file `.env` từ `.env.example` và cập nhật:
```env
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

### PHP Settings
File `docker/php/local.ini` chứa cấu hình PHP tùy chỉnh:
- Upload max: 50MB
- Memory limit: 512MB
- Execution time: 300s

### Nginx
File `docker/nginx/default.conf` cấu hình Nginx server.

## Troubleshooting

### Lỗi permission
```bash
# Sửa quyền cho storage và cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Lỗi database connection
```bash
# Kiểm tra database
docker-compose exec db mysql -u laravel -p laravel

# Restart database
docker-compose restart db
```

### Lỗi build Docker
Nếu gặp lỗi build:
```bash
# Xóa images cũ
docker-compose down --rmi all

# Build lại
docker-compose up -d --build
```

### Xem logs
```bash
# Logs của service cụ thể
docker-compose logs app
docker-compose logs web
docker-compose logs db

# Logs real-time
docker-compose logs -f app
```

### Kiểm tra health status
```bash
# Xem health của tất cả services
docker-compose ps

# Kiểm tra database connection
docker-compose exec app php artisan tinker --execute="DB::connection()->getPdo();"
```

## Production

Để deploy production:
1. Cập nhật `APP_ENV=production` trong `.env`
2. Tắt `APP_DEBUG=false`
3. Sử dụng HTTPS
4. Cấu hình backup database
5. Monitoring và logging
6. Sử dụng multi-stage build để giảm kích thước image

## Cấu trúc file

```
docker/
├── nginx/
│   └── default.conf      # Cấu hình Nginx
├── php/
│   └── local.ini        # Cấu hình PHP
└── init.sh              # Script khởi tạo Laravel
```
