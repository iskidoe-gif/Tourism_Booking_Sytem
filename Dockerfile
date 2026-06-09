# Simplified Dockerfile for Railway deployment using php artisan serve

# Stage 1: Build frontend assets with Node
FROM node:24-alpine AS node_builder
WORKDIR /app
COPY package*.json ./
COPY vite.config.js ./
COPY resources/ resources/
RUN npm ci --legacy-peer-deps
RUN npm run build || (echo "Build failed, trying alternative..." && npm run build -- --mode production)
RUN npm cache clean --force
# Verify build output
RUN ls -laR public/build && echo "Build completed successfully"

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
    libzip \
    libpng \
    oniguruma \
    libxml2 \
    libpq \
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
COPY --from=node_builder /app/public/build ./public/build

# Verify build assets were copied
RUN ls -laR public/build && test -f public/build/manifest.json || (echo "ERROR: Build assets not found" && exit 1)

# Setup Laravel directories
RUN mkdir -p storage bootstrap/cache && \
    chmod -R 777 storage bootstrap/cache

# Set environment variable for asset loading in production
# Leave ASSET_URL empty so Laravel uses relative paths
ENV ASSET_URL=""

# Copy and prepare entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:${PORT:-80}/health || exit 1

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]