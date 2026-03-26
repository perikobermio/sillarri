#!/usr/bin/env sh
set -eu

mkdir -p /tmp \
  /var/www/html/storage/logs \
  /var/www/html/storage/framework/cache/data \
  /var/www/html/storage/framework/sessions \
  /var/www/html/storage/framework/views \
  /var/www/html/storage/framework/testing \
  /var/www/html/bootstrap/cache

chmod 1777 /tmp || true
chmod -R 0777 /var/www/html/storage /var/www/html/bootstrap/cache || true

exec "$@"
