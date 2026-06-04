### Multi-stage Dockerfile for deploying Laravel app to Railway

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
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
COPY composer.json composer.lock ./
COPY . .
RUN cp .env.example .env || true
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 3) Final image with Apache + PHP
FROM php:8.2-apache

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

# Disable ALL MPM modules first
RUN a2dismod mpm_event mpm_worker mpm_prefork || true

# Enable only mpm_prefork
RUN a2enmod mpm_prefork

# Enable Apache rewrite
RUN a2enmod rewrite

# Remove conflicting Apache configs
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load || true
RUN rm -f /etc/apache2/mods-enabled/mpm_*.conf || true

# Ensure only mpm_prefork is loaded
RUN ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load
RUN ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

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

# Copy entrypoint helper for safe startup migrations
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Permissions for storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

