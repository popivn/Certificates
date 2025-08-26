#!/bin/bash

# Wait for database to be ready
echo "Waiting for database connection..."
while ! php artisan tinker --execute="DB::connection()->getPdo();" > /dev/null 2>&1; do
    echo "Database not ready, waiting..."
    sleep 2
done

echo "Database is ready!"

# Generate application key if not exists
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

# Generate APP_KEY if not exists
if ! grep -q "APP_KEY=base64:" .env; then
    echo "Generating APP_KEY..."
    php artisan key:generate
fi

# Run Laravel commands that were blocked during build
echo "Running Laravel package discovery..."
php artisan package:discover --ansi

echo "Running Laravel optimizations..."
php artisan optimize

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Create storage link if not exists
if [ ! -L public/storage ]; then
    echo "Creating storage link..."
    php artisan storage:link
fi

# Clear and cache config
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Laravel initialization completed!"

# Start PHP-FPM
exec php-fpm
