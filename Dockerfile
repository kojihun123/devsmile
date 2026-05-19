# ── Stage 1: Vite 빌드 ────────────────────────────────────────────────────────
FROM node:22-alpine AS frontend
WORKDIR /app

COPY package*.json ./
RUN npm ci --quiet

COPY resources/ ./resources/
COPY public/ ./public/
COPY vite.config.js postcss.config.js tailwind.config.js ./

RUN npm run build

# ── Stage 2: PHP-FPM ──────────────────────────────────────────────────────────
FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Stage 1에서 빌드된 assets 복사
COPY --from=frontend /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
