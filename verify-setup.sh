#!/bin/bash

echo "🔍 Verificando configuración para despliegue en Render..."

# Verificar archivos necesarios
FILES=(
    "Dockerfile"
    "apache-config.conf"
    "start.sh"
    "setup-production.sh"
    ".env.render"
    "php-production.ini"
    ".dockerignore"
    "render.yaml"
)

echo "📋 Verificando archivos necesarios..."
for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file existe"
    else
        echo "❌ $file NO existe"
    fi
done

# Verificar permisos de scripts
echo ""
echo "🔐 Verificando permisos de scripts..."
if [ -f "start.sh" ]; then
    chmod +x start.sh
    echo "✅ start.sh tiene permisos de ejecución"
fi

if [ -f "setup-production.sh" ]; then
    chmod +x setup-production.sh
    echo "✅ setup-production.sh tiene permisos de ejecución"
fi

# Verificar dependencias de Composer
echo ""
echo "📦 Verificando dependencias..."
if [ -f "composer.json" ]; then
    echo "✅ composer.json existe"
    if [ -f "composer.lock" ]; then
        echo "✅ composer.lock existe"
    else
        echo "⚠️ composer.lock no existe - ejecuta 'composer install' localmente"
    fi
else
    echo "❌ composer.json NO existe"
fi

# Verificar estructura de directorios Laravel
echo ""
echo "📁 Verificando estructura de Laravel..."
DIRS=("app" "config" "database" "public" "routes" "storage")
for dir in "${DIRS[@]}"; do
    if [ -d "$dir" ]; then
        echo "✅ Directorio $dir existe"
    else
        echo "❌ Directorio $dir NO existe"
    fi
done

# Verificar archivos de configuración importantes
echo ""
echo "⚙️ Verificando configuración de Laravel..."
CONFIG_FILES=("config/app.php" "config/database.php" "config/cors.php")
for file in "${CONFIG_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file existe"
    else
        echo "❌ $file NO existe"
    fi
done

# Verificar que storage es escribible
echo ""
echo "📝 Verificando permisos de storage..."
if [ -d "storage" ]; then
    if [ -w "storage" ]; then
        echo "✅ Directorio storage es escribible"
    else
        echo "⚠️ Directorio storage podría necesitar permisos de escritura"
    fi
    
    # Verificar subdirectorios de storage
    STORAGE_DIRS=("storage/app" "storage/framework" "storage/logs")
    for dir in "${STORAGE_DIRS[@]}"; do
        if [ -d "$dir" ]; then
            echo "✅ $dir existe"
        else
            echo "⚠️ $dir no existe - creándolo..."
            mkdir -p "$dir"
        fi
    done
else
    echo "❌ Directorio storage NO existe"
fi

# Verificar bootstrap/cache
echo ""
echo "🚀 Verificando cache de bootstrap..."
if [ -d "bootstrap/cache" ]; then
    echo "✅ bootstrap/cache existe"
else
    echo "⚠️ bootstrap/cache no existe - creándolo..."
    mkdir -p "bootstrap/cache"
fi

echo ""
echo "🎯 Verificación completada. Revisa los elementos marcados con ❌ o ⚠️"
echo ""
echo "📋 Próximos pasos:"
echo "1. Corrige cualquier problema encontrado"
echo "2. Sube los archivos a tu repositorio de Git"
echo "3. Crea un nuevo Web Service en Render"
echo "4. Configura las variables de entorno"
echo "5. ¡Despliega!"
