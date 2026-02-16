$ErrorActionPreference = "Stop"

Write-Host "Running full local quality gate..."

Write-Host "1/5 composer install"
composer install --no-interaction --prefer-dist

Write-Host "2/5 backend tests"
php artisan test

Write-Host "3/5 code style (pint)"
php ./vendor/bin/pint --test

Write-Host "4/5 npm ci"
npm ci

Write-Host "5/5 frontend build"
npm run build

Write-Host "All checks passed."
