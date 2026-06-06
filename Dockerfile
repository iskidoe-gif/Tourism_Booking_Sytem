# Multi-stage build for Laravel + Vite application

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
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Stage 3: Final runtime image
FROM php:8.2-fpm-alpine

# Install system packages and PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libzip-dev \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && apk del libzip-dev libpng-dev oniguruma-dev libxml2-dev

# Create runtime directories
RUN mkdir -p \
    /var/log/supervisor \
    /var/run/supervisor \
    /var/run/php \
    /var/cache/nginx \
    /var/log/nginx \
    /var/www/html

# Configure Nginx
RUN mkdir -p /etc/nginx/http.d
COPY <<'EOF' /etc/nginx/http.d/default.conf
server {
    listen 0.0.0.0:80;
    listen [::]:80;
    root /var/www/html/public;
    index index.php;
    # Allow unlimited request body size (large uploads handled with chunking)
    client_max_body_size 0;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF

# Configure Supervisor
COPY <<'EOF' /etc/supervisor/conf.d/supervisord.conf
[supervisord]
nodaemon=true
user=root

[program:php-fpm]
command=php-fpm
autostart=true
autorestart=true

[program:nginx]
command=/usr/sbin/nginx -g "daemon off;"
autostart=true
autorestart=true
EOF

WORKDIR /var/www/html

# Copy application
COPY . .

# Copy dependencies from builder stages
COPY --from=composer_builder /app/vendor ./vendor
COPY --from=node_builder /app/public ./public

# Setup Laravel directories
RUN mkdir -p storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 755 storage bootstrap/cache

# Copy and prepare entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
