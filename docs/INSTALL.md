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

### 3. Levantar los contenedores

```bash
docker compose up -d
```

Esto arranca los cuatro servicios: `padel-web`, `padel-app`, `padel-db` y `padel-node`.

### 4. Instalar dependencias PHP

```bash
docker exec -it padel-app composer install
```

### 5. Generar la clave de la aplicación

```bash
docker exec -it padel-app php artisan key:generate
```

### 6. Ejecutar migraciones y seeders

```bash
docker exec -it padel-app php artisan migrate --seed
```

Esto crea todas las tablas y carga los usuarios de prueba.

### 7. Instalar dependencias Node y compilar assets

```bash
docker exec -it padel-node npm install
docker exec -it padel-node npm run build
```

### 8. Acceder a la aplicación

Abre [http://localhost:8000](http://localhost:8000) en el navegador.

---

## Usuarios de prueba

| Email | Contraseña | Rol |
|---|---|---|
| admin@padel.com | Admin_padel123 | Administrador |
| coach@padel.com | Coach_padel123 | Entrenador |
| pepe@gmail.com | Pepe123 | Jugador |

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