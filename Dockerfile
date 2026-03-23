# ============================================================
# Stage 1: Builder — dependencias y assets compilados
# ============================================================
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

# ============================================================
FROM php:8.2-alpine AS composer-builder

RUN apk add --no-cache curl unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-req=ext-gd

COPY . .
RUN composer dump-autoload --optimize

# ============================================================
# Stage 2: Producción — PHP-FPM + Nginx + Supervisor
# ============================================================
FROM php:8.2-fpm-alpine

# Extensiones PHP necesarias para Laravel + MySQL
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        bcmath \
        opcache \
        pcntl

WORKDIR /var/www/html

# Copiar dependencias de Composer
COPY --from=composer-builder /app/vendor ./vendor
COPY --from=composer-builder /app .

# Copiar assets compilados (public/build)
COPY --from=node-builder /app/public/build ./public/build

# Copiar configuraciones
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh \
    && mkdir -p storage/framework/{cache,sessions,views} \
               storage/logs \
               bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
