#!/bin/sh

set -e

echo "Running Laravel setup..."

php artisan key:generate --force || true
php artisan migrate --force || true
php artisan storage:link || true
php artisan optimize:clear
php artisan optimize

echo "Starting services..."

php-fpm -D
nginx -g "daemon off;"
