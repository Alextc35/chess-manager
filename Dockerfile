FROM composer:2 AS composer

WORKDIR /app

COPY . .
RUN rm -f bootstrap/cache/*.php \
    && composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts \
    && php artisan package:discover --ansi

FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY vite.config.js ./
COPY public ./public
RUN npm run build

FROM php:8.3-apache

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev \
        unzip \
        libsqlite3-dev \
        sqlite3 \
    && docker-php-ext-install pdo_sqlite \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /app /var/www/html
COPY --from=frontend /app/public/build /var/www/html/public/build

RUN cp .env.example .env \
    && mkdir -p /data storage/framework/{views,cache,sessions} bootstrap/cache \
    && chown -R www-data:www-data /var/www/html /data \
    && chmod -R ug+rwx storage bootstrap/cache

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
