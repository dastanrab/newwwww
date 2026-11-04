#!/bin/sh
set -e

echo "===== Laravel Entrypoint Started ====="

cd /var/www

# حذف کش قدیمی پکیج‌ها و کانفیگ‌ها
rm -f bootstrap/cache/packages.php bootstrap/cache/services.php bootstrap/cache/config.php

# Wait for database
if [ -n "$DB_HOST" ]; then
  echo "Waiting for database..."
  until php -r "
    try {
        new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
    } catch (Exception \$e) {
        exit(1);
    }
  "; do
    sleep 2
  done
  echo "Database is ready."
fi

# Storage link
if [ ! -L "public/storage" ]; then
  echo "Creating storage symlink..."
  php artisan storage:link || true
fi

# Clear cache
echo "Clearing cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "===== Laravel Entrypoint Finished ====="

# Start Supervisor
exec "$@"
