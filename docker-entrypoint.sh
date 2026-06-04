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

# Optionally run database seeders (idempotent seeders recommended)
if [ "${RUN_SEEDS:-false}" = "true" ]; then
  echo "RUN_SEEDS is enabled — running database seeders..."
  seed_attempt=0
  seed_max=3

  until php artisan db:seed --class=DatabaseSeeder --force; do
    seed_attempt=$((seed_attempt + 1))
    echo "Seeding attempt $seed_attempt/$seed_max failed."

    if [ "$seed_attempt" -ge "$seed_max" ]; then
      echo "Seeding failed after $seed_attempt attempts. Continuing startup." 
      break
    fi

    echo "Waiting 5 seconds before retrying seeds..."
    sleep 5
  done
fi

exec apache2-foreground
