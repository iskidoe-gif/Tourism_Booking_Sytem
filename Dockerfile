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

# 3) Final image with PHP-FPM + Nginx + Supervisor
FROM php:8.2-fpm-alpine

# System deps and PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    libzip-dev \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    sqlite-dev \
    libpq-dev \
    zip \
    unzip \
    git \
 && docker-php-ext-install pdo pdo_mysql pdo_sqlite pdo_pgsql mbstring exif pcntl bcmath gd zip

# Create required runtime directories for Supervisor, PHP-FPM, and Nginx
RUN mkdir -p \
    /var/log/supervisor \
    /var/run/supervisor \
    /var/run/php \
    /var/cache/nginx \
    /var/log/nginx

# Nginx configuration for Laravel
RUN mkdir -p /etc/nginx/http.d
COPY <<'EOF' /etc/nginx/http.d/default.conf
server {
    listen 80;
    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

# Supervisor configuration to manage PHP-FPM and Nginx
RUN mkdir -p /etc/supervisor/conf.d
COPY <<'EOF' /etc/supervisor/conf.d/supervisord.conf
[supervisord]
nodaemon=true
user=root
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid

[program:php-fpm]
command=php-fpm
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:nginx]
command=nginx -g "daemon off;"
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
EOF

WORKDIR /var/www/html

# Copy application files
COPY . .

# Create .env file from example and generate APP_KEY
RUN cp .env.example .env && \
    php artisan key:generate --force || true

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

