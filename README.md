# PadelSync — Gestión de Clubes de Pádel

> Proyecto Final de Grado Superior DAW · Pablo Iglesias Peral · 2025/2026

PadelSync es una aplicación web para la gestión integral de un club de pádel. Digitaliza la operativa diaria desde tres perspectivas: administración del centro, gestión de la academia de entrenadores y experiencia del jugador.

[![Desplegado en Railway](https://img.shields.io/badge/Railway-000000?style=for-the-badge&logo=railway&logoColor=white)](https://railway.com/)

> **Versión en producción:** [https://prueba1-tfg-production.up.railway.app/](https://prueba1-tfg-production.up.railway.app/)

---

## Quickstart

```bash
# 1. Clonar el repositorio
git clone https://github.com/pabloiglepe/Prueba1---TFG.git
cd Prueba1---TFG

# 2. Copiar variables de entorno
cp src/.env.example src/.env

# 3. Levantar contenedores
docker compose up -d

# 4. Instalar dependencias y preparar la aplicación
docker exec -it padel-app composer install
docker exec -it padel-app php artisan key:generate
docker exec -it padel-app php artisan migrate --seed
docker exec -it padel-node npm install
docker exec -it padel-node npm run build
```

Accede en [http://localhost:8000](http://localhost:8000)

### Usuarios de prueba

| Email | Contraseña | Rol |
|---|---|---|
| admin@padel.com | Admin_padel123 | Administrador |
| coach@padel.com | Coach_padel123 | Entrenador |
| pepe@gmail.com | Pepe123 | Jugador |

---

## Stack tecnológico

| Capa | Tecnología |
|---|---|
| Backend | Laravel 12 + Livewire Volt |
| Frontend | Blade + Alpine.js + Tailwind CSS |
| Base de datos | MySQL 8 |
| Gráficos | ECharts (npm) |
| Iconos | Heroicons / FontAwesome / iconify-icon (npm) |
| Email | Brevo API HTTP |
| Scheduler (prod) | cron-job.org + endpoint protegido |
| Infraestructura | Docker (local) · Railway (producción) |

---

## Documentación

| Documento | Descripción |
|---|---|
| [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) | Arquitectura del sistema y stack tecnológico |
| [docs/INSTALL.md](docs/INSTALL.md) | Guía de instalación detallada |
| [docs/USAGE.md](docs/USAGE.md) | Manual de uso por roles |
| [DOCUMENTATION.md](DOCUMENTATION.md) | Documentación técnica completa |
| [BITACORA.md](BITACORA.md) | Registro de desarrollo y lecciones aprendidas |