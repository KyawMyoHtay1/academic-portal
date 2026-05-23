#!/bin/sh
set -eu

SCRIPT_DIR="$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)"

"$SCRIPT_DIR/bootstrap-runtime.sh"

cd /var/www/html
<<<<<<< HEAD
php artisan migrate --force

=======
php artisan optimize:clear
php artisan migrate --force
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
>>>>>>> 1e9f232bbdeac9084abc1815f7f1e7cc8a564a74
