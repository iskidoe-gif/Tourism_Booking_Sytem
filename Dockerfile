### Multi-stage Dockerfile for deploying Laravel app to Render

# 1) Node builder for frontend assets
FROM node:24 AS node_builder
WORKDIR /app
COPY package*.json ./
COPY vite.config.js ./
COPY resources resources
RUN npm ci --silent --legacy-peer-deps && npm run build

# 2) Composer builder for PHP dependencies
FROM composer:2 AS composer_builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 3) Final image with Apache + PHP
FROM php:8.1-apache

# System deps and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    libpq-dev \
    zip \
    unzip \
    git \
 && docker-php-ext-install pdo pdo_mysql pdo_sqlite pdo_pgsql mbstring exif pcntl bcmath gd zip

# Enable Apache rewrite
RUN a2enmod rewrite

# Ensure Apache serves from the Laravel `public` directory
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf

WORKDIR /var/www/html

# Copy application files
COPY . .

# Ensure SQLite database file exists (if using sqlite) and is writable
RUN mkdir -p database \
 && touch database/database.sqlite \
 && chown -R www-data:www-data database/database.sqlite || true

# Copy composer-installed vendor directory from builder
COPY --from=composer_builder /app/vendor ./vendor

# Copy built frontend assets from node builder (assumes Vite outputs to public/build)
COPY --from=node_builder /app/public ./public

# Permissions for storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 80
CMD ["apache2-foreground"]
