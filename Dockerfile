# Railway-compatible Dockerfile using nginx + php-fpm

# Stage 1: Build frontend assets with Node
FROM node:24-alpine AS node_builder
WORKDIR /app
COPY package*.json ./
COPY vite.config.js ./
COPY resources/ resources/
RUN npm ci --legacy-peer-deps && npm run build && \
    if [ -f public/build/.vite/manifest.json ] && [ ! -f public/build/manifest.json ]; then \
        cp public/build/.vite/manifest.json public/build/manifest.json; \
    fi && \
    npm cache clean --force

# Stage 2: Build PHP dependencies
FROM composer:2 AS composer_builder
WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Stage 3: Final runtime image
FROM php:8.2-fpm-alpine

# Install system packages and PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
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

WORKDIR /var/www/html

# Copy application
COPY . .

# Copy dependencies from builder stages
COPY --from=composer_builder /app/vendor ./vendor
COPY --from=node_builder /app/public/build ./public/build

# Setup Laravel directories
RUN mkdir -p storage bootstrap/cache /var/log/supervisor /var/run/supervisor && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 755 storage bootstrap/cache

# Copy and prepare entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copy supervisord configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Configure php-fpm to listen on TCP for nginx
RUN mkdir -p /usr/local/etc/php-fpm.d && \
    printf '[www]\nuser = www-data\ngroup = www-data\nlisten = 127.0.0.1:9000\nlisten.owner = www-data\nlisten.group = www-data\npm = dynamic\npm.max_children = 5\npm.start_servers = 2\npm.min_spare_servers = 1\npm.max_spare_servers = 3\n' > /usr/local/etc/php-fpm.d/www.conf

# Configure nginx
RUN mkdir -p /etc/nginx/conf.d && \
    printf 'server {\n    listen 0.0.0.0:__PORT__;\n    listen [::]:__PORT__;\n    root /var/www/html/public;\n    index index.php;\n    client_max_body_size 10G;\n\n    location / {\n        try_files $uri $uri/ /index.php?$query_string;\n    }\n\n    location ~ \.php$ {\n        fastcgi_pass 127.0.0.1:9000;\n        fastcgi_index index.php;\n        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n        include fastcgi_params;\n    }\n}\n' > /etc/nginx/conf.d/default.conf && \
    cat > /etc/nginx/nginx.conf <<'EOF'\nuser nginx;\nworker_processes auto;\npid /var/run/nginx.pid;\n\nevents {\n    worker_connections 1024;\n}\n\nhttp {\n    include /etc/nginx/mime.types;\n    default_type application/octet-stream;\n    sendfile on;\n    keepalive_timeout 65;\n    server_tokens off;\n\n    include /etc/nginx/conf.d/*.conf;\n}\nEOF

EXPOSE 8080
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost:${PORT:-8080}/health || exit 1

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]