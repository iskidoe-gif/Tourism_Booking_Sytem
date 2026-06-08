#!/bin/sh

echo "=== Starting Laravel container entrypoint ==="

# Use Railway's PORT or default to 80
PORT=${PORT:-80}
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

echo "=== Container startup complete ==="
echo "Ensuring storage and cache directories exist and have correct permissions..."
mkdir -p storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

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
echo "Starting PHP artisan serve on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT