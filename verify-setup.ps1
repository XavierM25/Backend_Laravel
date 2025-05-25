# Script de verificación para Windows PowerShell
Write-Host "🔍 Verificando configuración para despliegue en Render..." -ForegroundColor Green

# Verificar archivos necesarios
$files = @(
    "Dockerfile",
    "apache-config.conf", 
    "start.sh",
    "setup-production.sh",
    ".env.render",
    "php-production.ini",
    ".dockerignore",
    "render.yaml"
)

Write-Host "📋 Verificando archivos necesarios..." -ForegroundColor Yellow
foreach ($file in $files) {
    if (Test-Path $file) {
        Write-Host "✅ $file existe" -ForegroundColor Green
    } else {
        Write-Host "❌ $file NO existe" -ForegroundColor Red
    }
}

# Verificar dependencias de Composer
Write-Host ""
Write-Host "📦 Verificando dependencias..." -ForegroundColor Yellow
if (Test-Path "composer.json") {
    Write-Host "✅ composer.json existe" -ForegroundColor Green
    if (Test-Path "composer.lock") {
        Write-Host "✅ composer.lock existe" -ForegroundColor Green
    } else {
        Write-Host "⚠️ composer.lock no existe - ejecuta 'composer install' localmente" -ForegroundColor Yellow
    }
} else {
    Write-Host "❌ composer.json NO existe" -ForegroundColor Red
}

# Verificar estructura de directorios Laravel
Write-Host ""
Write-Host "📁 Verificando estructura de Laravel..." -ForegroundColor Yellow
$dirs = @("app", "config", "database", "public", "routes", "storage")
foreach ($dir in $dirs) {
    if (Test-Path -PathType Container $dir) {
        Write-Host "✅ Directorio $dir existe" -ForegroundColor Green
    } else {
        Write-Host "❌ Directorio $dir NO existe" -ForegroundColor Red
    }
}

# Verificar archivos de configuración importantes
Write-Host ""
Write-Host "⚙️ Verificando configuración de Laravel..." -ForegroundColor Yellow
$configFiles = @("config\app.php", "config\database.php", "config\cors.php")
foreach ($file in $configFiles) {
    if (Test-Path $file) {
        Write-Host "✅ $file existe" -ForegroundColor Green
    } else {
        Write-Host "❌ $file NO existe" -ForegroundColor Red
    }
}

# Verificar que storage existe
Write-Host ""
Write-Host "📝 Verificando directorios de storage..." -ForegroundColor Yellow
if (Test-Path -PathType Container "storage") {
    Write-Host "✅ Directorio storage existe" -ForegroundColor Green
    
    # Verificar subdirectorios de storage
    $storageDirs = @("storage\app", "storage\framework", "storage\logs")
    foreach ($dir in $storageDirs) {
        if (Test-Path -PathType Container $dir) {
            Write-Host "✅ $dir existe" -ForegroundColor Green
        } else {
            Write-Host "⚠️ $dir no existe - creándolo..." -ForegroundColor Yellow
            New-Item -ItemType Directory -Path $dir -Force | Out-Null
        }
    }
} else {
    Write-Host "❌ Directorio storage NO existe" -ForegroundColor Red
}

# Verificar bootstrap/cache
Write-Host ""
Write-Host "🚀 Verificando cache de bootstrap..." -ForegroundColor Yellow
if (Test-Path -PathType Container "bootstrap\cache") {
    Write-Host "✅ bootstrap\cache existe" -ForegroundColor Green
} else {
    Write-Host "⚠️ bootstrap\cache no existe - creándolo..." -ForegroundColor Yellow
    New-Item -ItemType Directory -Path "bootstrap\cache" -Force | Out-Null
}

# Verificar si Docker está disponible
Write-Host ""
Write-Host "🐳 Verificando Docker..." -ForegroundColor Yellow
try {
    $dockerVersion = docker --version 2>$null
    if ($dockerVersion) {
        Write-Host "✅ Docker está disponible: $dockerVersion" -ForegroundColor Green
    } else {
        Write-Host "⚠️ Docker no está disponible" -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠️ Docker no está disponible" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "🎯 Verificación completada. Revisa los elementos marcados con ❌ o ⚠️" -ForegroundColor Cyan
Write-Host ""
Write-Host "📋 Próximos pasos:" -ForegroundColor Blue
Write-Host "1. Corrige cualquier problema encontrado"
Write-Host "2. Sube los archivos a tu repositorio de Git"
Write-Host "3. Crea un nuevo Web Service en Render"
Write-Host "4. Configura las variables de entorno"
Write-Host "5. ¡Despliega!"
Write-Host ""
Write-Host "🧪 Para probar localmente: .\test-docker.ps1" -ForegroundColor Magenta
