#!/bin/sh
set -eu

SCRIPT_DIR="$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)"

"$SCRIPT_DIR/bootstrap-runtime.sh"

cd /var/www/html
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"

