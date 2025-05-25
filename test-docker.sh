#!/bin/bash

echo "🐳 Construyendo y ejecutando Laravel con Docker..."

# Construir la imagen
echo "📦 Construyendo imagen Docker..."
docker build -t laravel-render-app .

if [ $? -eq 0 ]; then
    echo "✅ Imagen construida exitosamente"
    
    # Ejecutar el contenedor
    echo "🚀 Iniciando contenedor..."
    docker run -d -p 8080:80 --name laravel-test laravel-render-app
    
    if [ $? -eq 0 ]; then
        echo "✅ Contenedor iniciado exitosamente"
        echo "🌐 La aplicación está disponible en: http://localhost:8080"
        echo ""
        echo "📋 Comandos útiles:"
        echo "  - Ver logs: docker logs laravel-test"
        echo "  - Parar contenedor: docker stop laravel-test"
        echo "  - Eliminar contenedor: docker rm laravel-test"
        echo "  - Acceder al contenedor: docker exec -it laravel-test bash"
    else
        echo "❌ Error al iniciar el contenedor"
    fi
else
    echo "❌ Error al construir la imagen"
fi
