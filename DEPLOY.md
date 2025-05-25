# Despliegue en Render con Docker

Este proyecto Laravel está configurado para ser desplegado en Render usando Docker con optimizaciones de producción.

## 📋 Archivos de configuración incluidos:

- `Dockerfile`: Configuración optimizada del contenedor Docker con multi-stage build
- `apache-config.conf`: Configuración de Apache con mod_rewrite
- `start.sh`: Script de inicio que maneja migraciones y configuración
- `setup-production.sh`: Script de configuración adicional para producción
- `render.yaml`: Configuración específica para Render
- `.env.render`: Variables de entorno optimizadas para Render
- `.env.production`: Variables de entorno para producción general
- `php-production.ini`: Configuración de PHP optimizada para producción
- `docker-compose.yml`: Para desarrollo local con Docker
- `test-docker.ps1` / `test-docker.sh`: Scripts para probar localmente
- `.dockerignore`: Archivos excluidos de la construcción Docker

## 🚀 Pasos para desplegar en Render:

### 1. Preparar el repositorio
1. Sube todos estos archivos a tu repositorio de Git
2. Asegúrate de que el repositorio esté público o tengas acceso en Render

### 2. Crear el servicio en Render
1. Ve a [Render Dashboard](https://dashboard.render.com/)
2. Haz clic en "New +" → "Web Service"
3. Conecta tu repositorio de GitHub/GitLab
4. Selecciona tu repositorio

### 3. Configurar el servicio
- **Environment**: Docker
- **Plan**: Free (o el que prefieras)
- **Build Command**: (dejar vacío)
- **Start Command**: (dejar vacío, se usa el CMD del Dockerfile)
- **Health Check Path**: `/`

### 4. Variables de entorno importantes
Añade estas variables de entorno en Render:
```bash
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:TuClaveGenerada
APP_URL=https://tu-app-name.onrender.com
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite
LOG_LEVEL=error
FRONTEND_URL=https://tu-frontend-app.onrender.com
```

### 5. Generar APP_KEY
Para generar una nueva APP_KEY, ejecuta localmente:
```bash
php artisan key:generate --show
```

### 6. Actualizar URLs
- En `.env.render`, cambia las URLs por tus dominios reales
- En `config/cors.php`, actualiza las URLs permitidas
- En `config/sanctum.php`, añade tu dominio si usas autenticación

## 🧪 Pruebas locales con Docker:

### En Windows (PowerShell):
```powershell
.\test-docker.ps1
```

### En Linux/Mac:
```bash
chmod +x test-docker.sh
./test-docker.sh
```

### Comandos manuales:
```bash
# Construir la imagen
docker build -t laravel-app .

# Ejecutar el contenedor
docker run -p 8080:80 laravel-app

# Ver en el navegador
http://localhost:8080
```

## ⚡ Optimizaciones incluidas:

✅ **Multi-stage build** para reducir tamaño de imagen  
✅ **OPcache habilitado** para mejor rendimiento PHP  
✅ **Configuración de producción** optimizada  
✅ **Base de datos SQLite** (sin configuración adicional)  
✅ **Migraciones automáticas** en el inicio  
✅ **Cache de configuración, rutas y vistas**  
✅ **Permisos correctos** para storage  
✅ **Apache con mod_rewrite** configurado  
✅ **Logs optimizados** para producción  
✅ **Health checks** incluidos  
✅ **CORS configurado** para frontend  

## 🔧 Troubleshooting:

### Si tienes problemas con permisos:
```bash
docker exec -it container_name chown -R www-data:www-data /var/www/html/storage
```

### Si necesitas ejecutar comandos Artisan:
```bash
docker exec -it container_name php artisan comando
```

### Si necesitas ver logs:
```bash
docker logs container_name
```

### Si la aplicación no responde:
1. Verifica que el puerto 80 esté expuesto
2. Revisa los logs de Apache: `docker exec -it container_name tail -f /var/log/apache2/error.log`
3. Verifica la configuración de base de datos

## 🗄️ Base de datos:

### SQLite (Por defecto):
- Configuración automática
- Archivo: `/var/www/html/database/database.sqlite`
- Ideal para aplicaciones pequeñas/medianas

### Para usar PostgreSQL en Render:
1. Crea una base de datos PostgreSQL en Render
2. Actualiza las variables de entorno:
```bash
DB_CONNECTION=pgsql
DB_HOST=tu-host-postgres
DB_PORT=5432
DB_DATABASE=tu-base-datos
DB_USERNAME=tu-usuario
DB_PASSWORD=tu-password
```
3. Modifica el Dockerfile para incluir `pdo_pgsql`

## 🔐 Seguridad:

- APP_DEBUG deshabilitado en producción
- Logs de errores enviados a archivos
- OPcache optimizado para producción
- Cookies seguras configuradas
- CORS configurado correctamente

## 📊 Monitoreo:

Render proporciona:
- Logs automáticos
- Métricas de CPU/memoria
- Health checks
- Alertas de downtime
