# Simplified Dockerfile for Railway deployment using php artisan serve

# Stage 1: Build frontend assets with Node
FROM node:24-alpine AS node_builder
WORKDIR /app
COPY package*.json ./
COPY vite.config.js ./
COPY resources/ resources/
RUN npm ci --legacy-peer-deps && npm run build && npm cache clean --force

# Stage 2: Build PHP dependencies
FROM composer:2 AS composer_builder
WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Stage 3: Final runtime image
FROM php:8.2-cli-alpine

# Install system packages and PHP extensions
RUN apk add --no-cache \
    curl \
    libzip-dev \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && apk del libzip-dev libpng-dev oniguruma-dev libxml2-dev

WORKDIR /app

# Copy application
COPY . .

# Copy dependencies from builder stages
COPY --from=composer_builder /app/vendor ./vendor
COPY --from=node_builder /app/public ./public

# Setup Laravel directories
RUN mkdir -p storage bootstrap/cache && \
    chmod -R 777 storage bootstrap/cache

# Copy and prepare entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:${PORT:-80}/health || exit 1

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]