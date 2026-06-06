#!/bin/sh

echo "=== Starting Laravel container entrypoint ==="

# Helper: run an artisan command, log output and any failure, but never exit
run_artisan() {
  CMD="$*"
  echo "[artisan] Running: php artisan $CMD"
  OUTPUT=$(php artisan $CMD 2>&1)
  STATUS=$?
  if [ $STATUS -ne 0 ]; then
    echo "[artisan] WARNING: 'php artisan $CMD' exited with status $STATUS"
    echo "[artisan] Output: $OUTPUT"
  else
    echo "[artisan] OK: $OUTPUT"
  fi
  return 0
}

# Generate APP_KEY if not set
echo "--- Checking APP_KEY ---"
if [ -z "$APP_KEY" ]; then
  echo "APP_KEY not set - generating..."
  GENERATED_KEY=$(php artisan key:generate --show --force 2>/dev/null)
  if [ -n "$GENERATED_KEY" ]; then
    export APP_KEY="$GENERATED_KEY"
    echo "Generated APP_KEY: ${APP_KEY:0:20}..."
  else
    echo "WARNING: artisan key:generate failed, falling back to openssl"
    export APP_KEY="base64:$(openssl rand -base64 32)"
    echo "Fallback APP_KEY: ${APP_KEY:0:20}..."
  fi
else
  echo "APP_KEY already set: ${APP_KEY:0:20}..."
fi

# Clear config cache so fresh env vars are picked up
echo "--- Clearing config cache ---"
run_artisan config:clear

# Run migrations if enabled (default: true)
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  echo "--- Running migrations ---"
  run_artisan migrate --force
else
  echo "--- Skipping migrations (RUN_MIGRATIONS=${RUN_MIGRATIONS}) ---"
fi

# Optionally run seeders (default: false)
if [ "${RUN_SEEDS:-false}" = "true" ]; then
  echo "--- Running seeders ---"
  run_artisan db:seed --force
else
  echo "--- Skipping seeders (RUN_SEEDS=${RUN_SEEDS:-false}) ---"
fi

echo "=== Laravel setup complete — starting Supervisor ==="
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
