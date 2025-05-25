# Script para probar Docker en Windows
Write-Host "🐳 Construyendo y ejecutando Laravel con Docker..." -ForegroundColor Green

# Construir la imagen
Write-Host "📦 Construyendo imagen Docker..." -ForegroundColor Yellow
docker build -t laravel-render-app .

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Imagen construida exitosamente" -ForegroundColor Green
    
    # Parar y eliminar contenedor existente si existe
    docker stop laravel-test 2>$null
    docker rm laravel-test 2>$null
    
    # Ejecutar el contenedor
    Write-Host "🚀 Iniciando contenedor..." -ForegroundColor Yellow
    docker run -d -p 8080:80 --name laravel-test laravel-render-app
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Contenedor iniciado exitosamente" -ForegroundColor Green
        Write-Host "🌐 La aplicación está disponible en: http://localhost:8080" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "📋 Comandos útiles:" -ForegroundColor Blue
        Write-Host "  - Ver logs: docker logs laravel-test"
        Write-Host "  - Parar contenedor: docker stop laravel-test"
        Write-Host "  - Eliminar contenedor: docker rm laravel-test"
        Write-Host "  - Acceder al contenedor: docker exec -it laravel-test bash"
        
        # Esperar un momento y verificar el estado
        Start-Sleep -Seconds 3
        $status = docker ps --filter "name=laravel-test" --format "table {{.Status}}"
        Write-Host "Estado del contenedor: $status" -ForegroundColor Magenta
    } else {
        Write-Host "❌ Error al iniciar el contenedor" -ForegroundColor Red
    }
} else {
    Write-Host "❌ Error al construir la imagen" -ForegroundColor Red
}
