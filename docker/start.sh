#!/usr/bin/env sh
set -e

mkdir -p storage bootstrap/cache database

# SQLite solo si se usa
if [ "$DB_CONNECTION" = "sqlite" ]; then
    touch "${DB_DATABASE:-database/database.sqlite}"
fi

# APP_KEY obligatorio
if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY no configurado (Render o docker -e)"
    exit 1
fi

# limpiar cache ANTES
php artisan view:clear
php artisan config:clear
php artisan cache:clear

# migraciones
php artisan migrate --force

# volver a cachear limpio
php artisan config:cache

# levantar server
php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"