#!/bin/sh

echo "=== Starting Laravel container entrypoint ==="

echo "Checking APP_KEY..."
if [ -z "$APP_KEY" ]; then
  echo "APP_KEY not set - generating..."
  php artisan key:generate --force 2>&1 || echo "WARNING: key generation failed"
  export APP_KEY=$(php artisan key:generate --show 2>/dev/null || echo "")
  if [ -z "$APP_KEY" ]; then
    echo "WARNING: Could not generate APP_KEY, using PHP fallback"
    export APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
  fi
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
