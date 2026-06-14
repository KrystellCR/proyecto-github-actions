#!/usr/bin/env sh
set -e

mkdir -p storage bootstrap/cache database

# SQLite solo si se usa
if [ "$DB_CONNECTION" = "sqlite" ]; then
    touch "${DB_DATABASE:-database/database.sqlite}"
fi

# SI NO EXISTE APP_KEY → error controlado
if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY no configurado (Render o docker -e)"
    exit 1
fi

php artisan migrate --force
php artisan config:cache

php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"