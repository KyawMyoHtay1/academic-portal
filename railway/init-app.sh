#!/bin/sh
set -eu

SCRIPT_DIR="$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)"

"$SCRIPT_DIR/bootstrap-runtime.sh"

cd /var/www/html
php artisan optimize:clear
php artisan migrate --force
php artisan config:cache
php artisan event:cache
php artisan view:cache
