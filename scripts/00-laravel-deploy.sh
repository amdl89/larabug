#!/usr/bin/env bash
echo "Running composer"
composer global require hirak/prestissimo
composer install --no-dev --working-dir=/var/www/html

echo "Optimize..."
php artisan optimize

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Preparing database..."
php artisan seed:database-from-fixtures
