# Use the official PHP image as a base image
FROM php:8.2-fpm

# Install system dependencies, Node.js, npm
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libjpeg-dev \
    libfreetype6-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Cài Chromium (Headless Chrome/Chromium cho chrome-php/chrome & Browsershot)
RUN apt-get update && apt-get install -y chromium \
    && rm -rf /var/lib/apt/lists/*

# Link chromium vào chrome (nếu cần)
RUN ln -s /usr/bin/chromium /usr/bin/google-chrome

# Install Puppeteer globally (Browsershot dependency)
RUN npm install -g puppeteer

# Create non-root user for running Puppeteer/Chrome
RUN groupadd -r appuser && useradd -r -g appuser -G audio,video appuser \
    && mkdir -p /home/appuser/Downloads \
    && chown -R appuser:appuser /home/appuser

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Install Laravel dependencies and required packages for Spatie\LaravelPdf
RUN composer install --optimize-autoloader --no-dev \
    && composer require chrome-php/chrome spatie/browsershot --no-scripts --no-interaction

# Create Laravel cache directories
RUN mkdir -p bootstrap/cache \
    && mkdir -p storage/framework/{cache,sessions,views} \
    && mkdir -p storage/logs \
    && mkdir -p storage/app/tmp_bulk_pdfs

# Set proper permissions
RUN chown -R appuser:appuser /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Switch to non-root user
USER appuser

# Expose port
EXPOSE 8000

# Start Laravel
CMD php artisan serve --host=0.0.0.0 --port=8000