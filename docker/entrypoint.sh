#!/bin/sh
set -e

echo "==> Generando clave de aplicación si no existe..."
php artisan key:generate --no-interaction --force

echo "==> Ejecutando migraciones..."
php artisan migrate --force --no-interaction

echo "==> Creando enlace de almacenamiento..."
php artisan storage:link --no-interaction 2>/dev/null || true

echo "==> Optimizando configuración..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Iniciando servicios..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
