#!/bin/sh
set -e

echo "===== Laravel Entrypoint Started ====="

cd /var/www

# Wait for database (optional but recommended)
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

# Storage link (safe)
if [ ! -L "public/storage" ]; then
  echo "Creating storage symlink..."
  php artisan storage:link || true
fi


# Clear & rebuild cache
echo "Clearing cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

if [ "$APP_ENV" = "production" ]; then
  echo "Caching config & routes..."
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
fi
echo "Publish..."
php artisan vendor:publish --all

echo "create scribe docs..."
php artisan scribe:generate
echo "===== Laravel Entrypoint Finished ====="

# Start Supervisor
exec "$@"
