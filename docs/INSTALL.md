# Guía de Instalación — PadelSync

## Requisitos previos

- **Docker Desktop** instalado y en ejecución
- **Git** para clonar el repositorio

---

## Instalación paso a paso

### 1. Clonar el repositorio

```bash
git clone https://github.com/pabloiglepe/Prueba1---TFG.git
cd Prueba1---TFG
```

### 2. Configurar variables de entorno

```bash
cp src/.env.example src/.env
```

Asegúrate de que las variables de base de datos coinciden con las del `docker-compose.yml`:

```env
DB_HOST=padel-db
DB_PORT=3306
DB_DATABASE=padel_club
DB_USERNAME=user_padel
DB_PASSWORD=user_pass
```

### 3. Configurar el envío de emails

Para que el sistema de recuperación de contraseña funcione con emails reales, la aplicación usa **Brevo API HTTP**. Esto es necesario tanto en local como en producción, ya que Railway bloquea las conexiones SMTP salientes.

**Paso 1** — Crea una cuenta gratuita en [brevo.com](https://brevo.com)

**Paso 2** — Ve a tu perfil → **SMTP & API** → pestaña **API Keys** → crea una API key con el nombre `PadelSync`

**Paso 3** — Añade estas variables en `src/.env`:

```env
MAIL_MAILER=brevo
MAIL_FROM_ADDRESS=tucuenta@gmail.com
MAIL_FROM_NAME="PadelSync"
BREVO_API_KEY=tu_api_key_de_brevo
```

> Si no configuras el email, los correos se escribirán en `storage/logs/laravel.log` en lugar de enviarse. Cambia `MAIL_MAILER=log` para ese comportamiento.

> **¿Por qué Brevo API y no SMTP?** Railway bloquea todos los puertos SMTP salientes (25, 465, 587). El transport personalizado de Brevo usa la API HTTP sobre HTTPS (puerto 443), que siempre está abierto. Ver Hito 13 en `BITACORA.md` para más detalles.

### 4. Levantar los contenedores

```bash
docker compose up -d
```

Esto arranca los cuatro servicios: `padel-web`, `padel-app`, `padel-db` y `padel-node`.

### 5. Instalar dependencias PHP

```bash
docker exec -it padel-app composer install
```

### 6. Generar la clave de la aplicación

```bash
docker exec -it padel-app php artisan key:generate
```

### 7. Ejecutar migraciones y seeders

```bash
docker exec -it padel-app php artisan migrate --seed
```

Esto crea todas las tablas y carga los usuarios de prueba.

### 8. Instalar dependencias Node y compilar assets

```bash
docker exec -it padel-node npm install
docker exec -it padel-node npm run build
```

### 9. Acceder a la aplicación

Abre [http://localhost:8000](http://localhost:8000) en el navegador.

---

## Usuarios de prueba

| Email | Contraseña | Rol |
|---|---|---|
| admin@padel.com | Admin_padel123 | Administrador |
| coach@padel.com | Coach_padel123 | Entrenador |
| pepe@gmail.com | Pepe123 | Jugador |

---

## Variables de entorno de Railway (producción)

En el panel de Railway → tu servicio → **Variables**, añade:

```
MAIL_MAILER=brevo
MAIL_FROM_ADDRESS=tucuenta@gmail.com
MAIL_FROM_NAME=PadelSync
BREVO_API_KEY=tu_api_key_de_brevo
```

---

## Comandos útiles durante el desarrollo

```bash
# Ver logs de la aplicación
docker exec -it padel-app tail -f storage/logs/laravel.log

# Recompilar assets tras cambios en vistas
docker exec -it padel-node npm run build

# Limpiar caché de Laravel
docker exec -it padel-app php artisan cache:clear
docker exec -it padel-app php artisan view:clear
docker exec -it padel-app php artisan config:clear

# Acceder al contenedor PHP
docker exec -it padel-app bash

# Resetear la base de datos
docker exec -it padel-app php artisan migrate:fresh --seed

# Parar los contenedores
docker compose down

# Parar y eliminar volúmenes
docker compose down -v
```

---

## Sincronizar base de datos local con Railway

Si necesitas importar tu BD local en Railway:

```bash
# 1. Exportar la BD local
docker exec -it padel-db mysqldump -u user_padel -puser_pass padel_club > backup.sql

# 2. Limpiar warnings del dump (importante, o fallará la importación)
grep -v "^mysqldump:" backup.sql > backup_clean.sql

# 3. Importar en Railway (sustituye con tus credenciales reales)
mysql -h caboose.proxy.rlwy.net -P 58770 -u root -pTU_PASSWORD --skip-ssl railway < backup_clean.sql
```

Las credenciales de Railway las encuentras en el panel → servicio MySQL → pestaña **Variables**.

---

## Solución de problemas comunes

**Error 500 tras instalar**  
Dar permisos de escritura a las carpetas de Laravel:
```bash
docker exec -it padel-app chmod -R 775 storage bootstrap/cache
```

**Los estilos no se aplican**  
Recompilar los assets:
```bash
docker exec -it padel-node npm run build
```

**Error de conexión a la base de datos**  
Verificar que las variables `DB_*` en `src/.env` coinciden exactamente con las del `docker-compose.yml`.

**Los emails de recuperación de contraseña no llegan**  
Verificar que `MAIL_MAILER=brevo` y que `BREVO_API_KEY` contiene la API key (no la contraseña SMTP). Si el problema persiste, revisar `storage/logs/laravel.log` para ver el error exacto.

**Error `Unsupported mail transport [brevo]`**  
Asegúrate de que el transport está registrado en `AppServiceProvider@boot` y que existe el archivo `app/Mail/BrevoTransport.php`.