#!/bin/sh
set -eu

<<<<<<< HEAD
mkdir -p \
  /var/www/html/storage/app/private \
  /var/www/html/storage/app/public \
  /var/www/html/storage/framework/cache/data \
  /var/www/html/storage/framework/sessions \
  /var/www/html/storage/framework/views \
  /var/www/html/storage/logs \
  /var/www/html/bootstrap/cache

if [ ! -e /var/www/html/public/storage ]; then
  ln -s /var/www/html/storage/app/public /var/www/html/public/storage
fi

if [ "$(id -u)" = "0" ]; then
  chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
fi

php /var/www/html/artisan package:discover --ansi
=======
APP_ROOT="/var/www/html"
SQLITE_DB="$APP_ROOT/database/database.sqlite"
SEEDED_FRESH_SQLITE=0

generate_app_key() {
  php -r "echo 'base64:'.base64_encode(random_bytes(32));"
}

configure_redis_defaults() {
  if [ -z "${REDIS_URL:-}" ] && [ -z "${REDIS_HOST:-}" ]; then
    return
  fi

  export SESSION_DRIVER="${SESSION_DRIVER:-redis}"
  export SESSION_CONNECTION="${SESSION_CONNECTION:-default}"
  export SESSION_STORE="${SESSION_STORE:-redis}"
  export CACHE_STORE="${CACHE_STORE:-redis}"
  export QUEUE_CONNECTION="${QUEUE_CONNECTION:-redis}"
  export REDIS_QUEUE_CONNECTION="${REDIS_QUEUE_CONNECTION:-default}"
  export REDIS_QUEUE="${REDIS_QUEUE:-default}"
  export REDIS_CACHE_CONNECTION="${REDIS_CACHE_CONNECTION:-cache}"
  export REDIS_CACHE_LOCK_CONNECTION="${REDIS_CACHE_LOCK_CONNECTION:-default}"
}

configure_apache_port() {
  if [ -z "${PORT:-}" ] || [ "${PORT}" = "80" ] || [ ! -f /etc/apache2/ports.conf ]; then
    return
  fi

  sed -ri "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf

  for site in /etc/apache2/sites-available/*.conf /etc/apache2/sites-enabled/*.conf; do
    if [ -f "$site" ]; then
      sed -ri "s/<VirtualHost \\*:80>/<VirtualHost *:${PORT}>/g" "$site"
    fi
  done
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

configure_redis_defaults

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

php "$APP_ROOT/artisan" config:cache
php "$APP_ROOT/artisan" event:cache
php "$APP_ROOT/artisan" route:cache
php "$APP_ROOT/artisan" view:cache

if [ "${1:-}" = "apache2-foreground" ] && [ -d /etc/apache2/mods-enabled ]; then
  rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf
  ln -sf ../mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load

  if [ -f /etc/apache2/mods-available/mpm_prefork.conf ]; then
    ln -sf ../mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf
  fi

  configure_apache_port
fi
>>>>>>> 1e9f232bbdeac9084abc1815f7f1e7cc8a564a74

exec "$@"
