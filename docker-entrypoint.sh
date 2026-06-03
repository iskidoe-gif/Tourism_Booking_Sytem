#!/bin/sh
set -e

echo "Starting Laravel container entrypoint..."

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  echo "Running database migrations..."
  attempt=0
  max_attempts=5

  until php artisan migrate --force; do
    attempt=$((attempt + 1))
    echo "Migration attempt $attempt/$max_attempts failed."

    if [ "$attempt" -ge "$max_attempts" ]; then
      echo "Migrations failed after $attempt attempts. Continuing startup without blocking."
      break
    fi

    echo "Waiting 5 seconds before retrying..."
    sleep 5
  done
fi

exec apache2-foreground
