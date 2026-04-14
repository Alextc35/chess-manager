FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libicu-dev \
    libsqlite3-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo_sqlite bcmath intl mbstring zip exif pcntl xml \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

CMD ["sh", "-lc", "composer install && cp .env.example .env 2>/dev/null || true && mkdir -p database && touch database/database.sqlite && php artisan key:generate --force || true && php artisan migrate --force && npm install && npm run build && php artisan serve --host=0.0.0.0 --port=8000"]