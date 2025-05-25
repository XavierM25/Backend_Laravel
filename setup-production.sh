#!/bin/bash

echo "🚀 Configurando Laravel para producción en Render..."

# Verificar que existe el archivo .env
if [ ! -f .env ]; then
    echo "❌ No se encontró archivo .env"
    exit 1
fi

# Verificar que APP_KEY está configurada
if ! grep -q "APP_KEY=base64:" .env; then
    echo "⚙️ Generando APP_KEY..."
    php artisan key:generate --force
fi

# Optimizar configuración para producción
echo "⚙️ Optimizando configuración..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verificar permisos
echo "🔐 Configurando permisos..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force

# Crear enlace simbólico para storage si no existe
if [ ! -L public/storage ]; then
    echo "🔗 Creando enlace simbólico para storage..."
    php artisan storage:link
fi

# Verificar que la base de datos existe y es accesible
echo "🔍 Verificando conexión a base de datos..."
if ! php artisan migrate:status > /dev/null 2>&1; then
    echo "❌ Error en la conexión a la base de datos"
    exit 1
fi

echo "✅ Configuración completada exitosamente"
