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
  APP_KEY_PREVIEW=$(printf '%s' "$APP_KEY" | cut -c 1-20)
  echo "Generated APP_KEY: ${APP_KEY_PREVIEW}..."
fi

APP_KEY_PREVIEW=$(printf '%s' "$APP_KEY" | cut -c 1-20)
echo "APP_KEY is set: ${APP_KEY_PREVIEW}..."

echo "Database Configuration:"
echo "DB_CONNECTION: ${DB_CONNECTION:-not set}"
echo "DB_HOST: ${DB_HOST:-not set}"
echo "DB_DATABASE: ${DB_DATABASE:-not set}"

# Test database connection before running migrations
echo "Testing database connection..."
if php artisan db:show 2>&1 | grep -q "Database:"; then
  echo "Database connection successful"
else
  echo "WARNING: Database connection failed or database not configured"
  echo "Please ensure Railway database variables are set (DB_CONNECTION, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)"
fi

echo "Clearing config cache..."
php artisan config:clear 2>&1 || echo "WARNING: config cache clear failed"

echo "Caching config..."
php artisan config:cache 2>&1 || echo "WARNING: config cache failed"

echo "Caching routes..."
php artisan route:cache 2>&1 || echo "WARNING: route cache failed"

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  echo "Running migrations..."
  php artisan migrate --force 2>&1
  MIGRATION_STATUS=$?
  if [ $MIGRATION_STATUS -ne 0 ]; then
    echo "ERROR: Migrations failed with status $MIGRATION_STATUS"
    echo "Application may not function correctly without database tables"
  else
    echo "Migrations completed successfully"
  fi
fi

if [ "${RUN_SEEDS:-true}" = "true" ]; then
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
mkdir -p storage/logs bootstrap/cache storage/framework/sessions storage/framework/views
chmod -R 777 storage bootstrap/cache

# Check if frontend assets exist
if [ ! -f "public/build/manifest.json" ]; then
    echo "WARNING: Frontend assets not found. The UI may not load correctly."
    echo "Expected file: public/build/manifest.json"
    ls -la public/ 2>&1 || echo "public directory not found"
    ls -la public/build/ 2>&1 || echo "public/build directory not found"
else
    echo "Frontend assets found successfully"
fi

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
