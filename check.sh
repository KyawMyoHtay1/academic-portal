#!/usr/bin/env sh
set -eu

echo "Running full local quality gate..."

echo "1/5 composer install"
composer install --no-interaction --prefer-dist

echo "2/5 backend tests"
php artisan test

echo "3/5 code style (pint)"
php ./vendor/bin/pint --test

echo "4/5 npm ci"
npm ci

echo "5/5 frontend build"
npm run build

echo "All checks passed."
