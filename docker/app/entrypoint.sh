#!/usr/bin/env bash
set -e

# Install composer dependencies if vendor missing
if [ ! -d "vendor" ]; then
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Generate app key if needed
if [ ! -f .env ]; then
  cp .env.example .env
  php artisan key:generate
fi

# Ensure SQLite database path exists
mkdir -p database
NEW_DB=0
if [ ! -f database/database.sqlite ]; then
  touch database/database.sqlite
  NEW_DB=1
elif [ ! -s database/database.sqlite ]; then
  NEW_DB=1
fi

# Ensure writable directories for Laravel
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache database || true
chmod -R ug+rwX storage bootstrap/cache database || true

# Ensure storage link only if missing
if [ ! -L public/storage ]; then
  php artisan storage:link || true
fi

# Run migrations (non-destructive in dev)
php artisan migrate --force || true

# Seed only on first boot when database is new/empty
if [ "$NEW_DB" -eq 1 ] && [ -d database/seeders ]; then
  php artisan db:seed --force || true
fi

# Hand off to the container CMD (e.g., php-fpm)
exec "$@"
