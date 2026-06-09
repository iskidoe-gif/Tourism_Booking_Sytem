#!/bin/sh

echo "=== Starting Laravel container entrypoint ==="

# Use Railway's PORT or default to 8080
PORT=${PORT:-8080}
export PORT
echo "Using PORT: $PORT"

echo "Checking APP_KEY..."
if [ -z "$APP_KEY" ]; then
  echo "APP_KEY not set - generating..."
  export APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
  echo "Generated APP_KEY: ${APP_KEY:0:20}..."
fi

echo "APP_KEY is set: ${APP_KEY:0:20}..."

echo "Clearing config cache..."
php artisan config:clear 2>&1 || echo "WARNING: config cache clear failed"

echo "Caching config..."
php artisan config:cache 2>&1 || echo "WARNING: config cache failed"

echo "Caching routes..."
php artisan route:cache 2>&1 || echo "WARNING: route cache failed"

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  echo "Running migrations..."
  php artisan migrate --force 2>&1 || echo "WARNING: migrations failed (app will start anyway)"
fi

if [ "${RUN_SEEDS:-false}" = "true" ]; then
  echo "Running seeders..."
  php artisan db:seed --force 2>&1 || echo "WARNING: seeders failed"
fi

# Repair Vite manifest location after the node build stage, if it was generated inside .vite
if [ -f public/build/.vite/manifest.json ] && [ ! -f public/build/manifest.json ]; then
  echo "Repairing Vite manifest location..."
  mkdir -p public/build
  cp public/build/.vite/manifest.json public/build/manifest.json
fi

echo "=== Container startup complete ==="
echo "Ensuring storage and cache directories exist and have correct permissions..."
mkdir -p storage/logs bootstrap/cache
chmod -R 777 storage bootstrap/cache

# Allow forcing debug mode for short-lived troubleshooting via FORCE_APP_DEBUG env var
if [ "${FORCE_APP_DEBUG:-false}" = "true" ]; then
  echo "FORCE_APP_DEBUG enabled — exporting APP_DEBUG=true"
  export APP_DEBUG=true
fi

echo "Recent application logs (last 200 lines):"
if [ -f storage/logs/laravel.log ]; then
  tail -n 200 storage/logs/laravel.log || true
else
  echo "(no laravel.log present yet)"
fi
if [ -f /etc/nginx/conf.d/default.conf ]; then
  sed -i "s/__PORT__/${PORT}/g" /etc/nginx/conf.d/default.conf
fi

echo "Starting supervisord to launch nginx and php-fpm..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf