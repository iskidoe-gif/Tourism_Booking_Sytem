#!/bin/sh
set -e

echo "=== Starting Laravel container entrypoint ==="

# Generate APP_KEY if not set
echo "Checking APP_KEY..."
if [ -z "$APP_KEY" ]; then
  echo "APP_KEY not set - generating..."
  php artisan key:generate --force 2>&1 || true
  # Extract the generated key
  export APP_KEY=$(php artisan key:generate --show 2>/dev/null || echo "")
  if [ -z "$APP_KEY" ]; then
    echo "WARNING: Could not generate APP_KEY, using base64:... format"
    export APP_KEY="base64:$(openssl rand -base64 32)"
  fi
  echo "Generated APP_KEY: ${APP_KEY:0:20}..."
fi

echo "APP_KEY is set: ${APP_KEY:0:20}..."

# Clear config cache
echo "Clearing config cache..."
php artisan config:clear 2>&1 || true

# Run migrations if enabled
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  echo "Running migrations..."
  php artisan migrate --force 2>&1 || true
fi

# Optionally run seeders
if [ "${RUN_SEEDS:-false}" = "true" ]; then
  echo "Running seeders..."
  php artisan db:seed --force 2>&1 || true
fi

echo "=== Container startup complete ==="
echo "Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
