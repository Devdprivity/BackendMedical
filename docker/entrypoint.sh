#!/bin/sh
set -e

echo "==> Generando clave de aplicación si no existe..."
if [ ! -f .env ]; then
    cp .env.example .env 2>/dev/null || touch .env
fi
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --no-interaction --force
else
    echo "APP_KEY ya está definida como variable de entorno, omitiendo key:generate"
fi

echo "==> Ejecutando migraciones..."
php artisan migrate --force --no-interaction

echo "==> Actualizando planes de suscripción..."
php artisan db:seed --class=SubscriptionPlansSeeder --force

echo "==> Creando enlace de almacenamiento..."
php artisan storage:link --no-interaction 2>/dev/null || true

echo "==> Optimizando configuración..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Iniciando servicios..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
