#!/bin/sh
set -eu

APP_ROOT="/var/www/html"

<<<<<<< HEAD
=======
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

configure_redis_defaults

>>>>>>> 1e9f232bbdeac9084abc1815f7f1e7cc8a564a74
mkdir -p \
  "$APP_ROOT/storage/app/private" \
  "$APP_ROOT/storage/app/public" \
  "$APP_ROOT/storage/framework/cache/data" \
  "$APP_ROOT/storage/framework/sessions" \
  "$APP_ROOT/storage/framework/views" \
  "$APP_ROOT/storage/logs" \
  "$APP_ROOT/bootstrap/cache"

if [ ! -e "$APP_ROOT/public/storage" ]; then
  ln -s "$APP_ROOT/storage/app/public" "$APP_ROOT/public/storage"
fi

chmod -R ug+rwx "$APP_ROOT/storage" "$APP_ROOT/bootstrap/cache" || true
<<<<<<< HEAD

=======
>>>>>>> 1e9f232bbdeac9084abc1815f7f1e7cc8a564a74
