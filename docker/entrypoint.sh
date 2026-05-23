#!/bin/sh
set -eu

APP_ROOT="/var/www/html"
SQLITE_DB="$APP_ROOT/database/database.sqlite"
SEEDED_FRESH_SQLITE=0

generate_app_key() {
  php -r "echo 'base64:'.base64_encode(random_bytes(32));"
}

mkdir -p \
  "$APP_ROOT/storage/app/private" \
  "$APP_ROOT/storage/app/public" \
  "$APP_ROOT/storage/framework/cache/data" \
  "$APP_ROOT/storage/framework/sessions" \
  "$APP_ROOT/storage/framework/views" \
  "$APP_ROOT/storage/logs" \
  "$APP_ROOT/bootstrap/cache"

if [ -z "${APP_KEY:-}" ]; then
  export APP_KEY="$(generate_app_key)"
fi

if [ -z "${DB_CONNECTION:-}" ]; then
  export DB_CONNECTION=sqlite
fi

export QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"
export MAIL_MAILER="${MAIL_MAILER:-log}"
export SEED_DEMO_DATA="${SEED_DEMO_DATA:-false}"

if [ "$DB_CONNECTION" = "sqlite" ]; then
  mkdir -p "$APP_ROOT/database"

  if [ ! -f "$SQLITE_DB" ]; then
    touch "$SQLITE_DB"
    SEEDED_FRESH_SQLITE=1
  fi

  if [ -z "${DB_DATABASE:-}" ]; then
    export DB_DATABASE="$SQLITE_DB"
  fi

  export SESSION_DRIVER="${SESSION_DRIVER:-file}"
  export CACHE_STORE="${CACHE_STORE:-file}"
fi

if [ ! -e "$APP_ROOT/public/storage" ]; then
  ln -s "$APP_ROOT/storage/app/public" "$APP_ROOT/public/storage"
fi

if [ "$(id -u)" = "0" ]; then
  chown -R www-data:www-data "$APP_ROOT/storage" "$APP_ROOT/bootstrap/cache" "$APP_ROOT/database"
fi

php "$APP_ROOT/artisan" package:discover --ansi

attempt=0
until php "$APP_ROOT/artisan" migrate --force; do
  attempt=$((attempt + 1))
  if [ "$attempt" -ge 15 ]; then
    echo "Migration failed after $attempt attempts." >&2
    exit 1
  fi
  sleep 2
done

if [ "$SEEDED_FRESH_SQLITE" -eq 1 ]; then
  php "$APP_ROOT/artisan" db:seed --class=ComprehensiveDemoSeeder --force
fi

if [ "$SEED_DEMO_DATA" = "true" ] || [ "$SEED_DEMO_DATA" = "1" ]; then
  php "$APP_ROOT/artisan" db:seed --force
fi

if [ "${1:-}" = "apache2-foreground" ] && [ -d /etc/apache2/mods-enabled ]; then
  rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf
  ln -sf ../mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load

  if [ -f /etc/apache2/mods-available/mpm_prefork.conf ]; then
    ln -sf ../mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf
  fi
fi

exec "$@"
