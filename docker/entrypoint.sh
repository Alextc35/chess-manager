#!/bin/sh
set -e

if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

if grep -q '^APP_KEY=$' /var/www/html/.env; then
    php artisan key:generate --force --no-interaction
fi

if [ -n "${DB_DATABASE}" ]; then
    mkdir -p "$(dirname "${DB_DATABASE}")"
    touch "${DB_DATABASE}"
fi

php artisan migrate --seed --force --no-interaction

exec "$@"
