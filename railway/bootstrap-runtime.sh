#!/bin/sh
set -eu

APP_ROOT="/var/www/html"

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

