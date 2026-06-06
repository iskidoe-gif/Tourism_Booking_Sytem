#!/bin/sh

echo "=== Starting Laravel container entrypoint ==="

# Use Railway's PORT or default to 80
PORT=${PORT:-80}
echo "Using PORT: $PORT"

# Dynamically write nginx config with correct port
mkdir -p /etc/nginx/http.d
cat > /etc/nginx/http.d/default.conf <<NGINX
server {
    listen 0.0.0.0:${PORT};
    listen [::]:${PORT};
    root /var/www/html/public;
    index index.php;
    client_max_body_size 10G;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
NGINX

echo "Nginx config written for port $PORT"

echo "Checking APP_KEY..."
if [ -z "$APP_KEY" ]; then
  echo "APP_KEY not set - generating..."
  export APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
  echo "Generated APP_KEY: ${APP_KEY:0:20}..."
fi

echo "APP_KEY is set: ${APP_KEY:0:20}..."

echo "Clearing config cache..."
php artisan config:clear 2>&1 || echo "WARNING: config cache clear failed"

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  echo "Running migrations..."
  php artisan migrate --force 2>&1 || echo "WARNING: migrations failed"
fi

if [ "${RUN_SEEDS:-false}" = "true" ]; then
  echo "Running seeders..."
  php artisan db:seed --force 2>&1 || echo "WARNING: seeders failed"
fi

echo "=== Container startup complete ==="
echo "Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisord.conf