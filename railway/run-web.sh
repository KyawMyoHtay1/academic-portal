#!/bin/sh
set -eu

SCRIPT_DIR="$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)"

"$SCRIPT_DIR/bootstrap-runtime.sh"

cd /var/www/html

if command -v apache2-foreground >/dev/null 2>&1 && [ -f /etc/apache2/ports.conf ]; then
  if [ -n "${PORT:-}" ] && [ "${PORT}" != "80" ]; then
    sed -ri "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf

    for site in /etc/apache2/sites-available/*.conf /etc/apache2/sites-enabled/*.conf; do
      if [ -f "$site" ]; then
        sed -ri "s/<VirtualHost \\*:80>/<VirtualHost *:${PORT}>/g" "$site"
      fi
    done
  fi

  exec apache2-foreground
fi

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
