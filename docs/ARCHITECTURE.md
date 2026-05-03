# Arquitectura del Sistema — PadelSync

## Stack tecnológico

| Capa | Tecnología | Versión |
|---|---|---|
| Backend | Laravel | 12.0 |
| Frontend | Blade + Livewire Volt | — |
| Base de datos | MySQL | 8.0 |
| Servidor web | Nginx | Alpine |
| Runtime PHP | PHP-FPM | 8.2 |
| Compilación assets | Vite + Tailwind CSS | — |
| Gráficos | ECharts | 5.6.0 (npm) |
| Iconos | Heroicons / FontAwesome / iconify-icon | SVG inline / npm |
| Alertas | SweetAlert2 | CDN |
| Contenerización | Docker + Docker Compose | — |
| Despliegue | Railway | — |
| Scheduler (producción) | cron-job.org | — |
| Control de versiones | Git + GitHub | — |

---

## Contenedores Docker

La aplicación se divide en cuatro servicios interconectados mediante la red interna `padel-network`:

```
┌─────────────────────────────────────────────┐
│                padel-network                │
│                                             │
│  ┌──────────┐    ┌──────────┐    ┌────────┐ │
│  │  Nginx   │───▶│ PHP-FPM  │───▶│ MySQL  │ │
│  │(padel-web│    │(padel-app│    │(padel- │ │
│  │ :8000)   │    │ :9000)   │    │ db)    │ │
│  └──────────┘    └──────────┘    └────────┘ │
│                       │                     │
│                  ┌──────────┐               │
│                  │  Node.js │               │
│                  │(padel-   │               │
│                  │  node)   │               │
│                  └──────────┘               │
└─────────────────────────────────────────────┘
```

| Servicio | Imagen | Función |
|---|---|---|
| `padel-web` | Nginx Alpine | Sirve assets estáticos |
| `padel-app` | PHP 8.2-FPM | Backend Laravel, lógica de negocio |
| `padel-db` | MySQL 8.0 | Persistencia de datos |
| `padel-node` | Node.js 20 | Compilación de assets con Vite (ECharts, iconify-icon) |

### Puertos expuestos

| Servicio | Puerto externo | Puerto interno |
|---|---|---|
| Nginx (web) | 8000 | 80 |
| MySQL | 3307 | 3306 |
| Vite dev server | 5173 | 5173 |

---

## Patrón MVC

La aplicación sigue el patrón **MVC** de Laravel con la siguiente organización de controladores:

```
app/Http/Controllers/
├── Admin/
│   ├── CourtController.php       # CRUD de pistas
│   ├── DashboardController.php   # KPIs y gráficos
│   ├── UserController.php        # Gestión de usuarios
│   └── ExportController.php      # Exportación Excel
├── Coach/
│   └── ClassController.php       # Gestión de clases
├── Player/
│   ├── ReservationController.php # Motor de reservas
│   └── ClassController.php       # Inscripción a clases
├── ProfileController.php         # Perfil de usuario
└── RedirectController.php        # Redirección por rol
```

---

## Scheduler — Tareas programadas

El sistema dispone de comandos Artisan que se ejecutan periódicamente para mantener los estados de la base de datos sincronizados:

| Comando | Acción | Frecuencia |
|---|---|---|
| `classes:complete-finished` | Marca como `completed` las clases finalizadas | Cada 15 min |
| `reservations:complete-finished` | Marca como `paid` las reservas pasadas en `pending` | Cada 15 min |
| `weather:fetch` | Obtiene datos meteorológicos de Open-Meteo para los próximos 14 días | Diaria (06:00) |
| `db:backup` | Genera un backup SQL de la BD en `storage/app/backups/` | Diaria (03:00) |

Los comandos están registrados en `routes/console.php`.

### Ejecución local

```bash
docker exec -it padel-app php artisan schedule:run
```

### Ejecución en producción (Railway)

Railway no ofrece cron jobs nativos en el plan gratuito. La solución implementada combina un **endpoint HTTP protegido** en la aplicación con el servicio externo **cron-job.org**:

```
cron-job.org  ──(cada 15 min)──▶  /run-scheduler  ──▶  php artisan schedule:run
                                   (header X-Cron-Secret)
```

El endpoint verifica el header `X-Cron-Secret` contra `config('padelsync.cron_secret')` antes de ejecutar el scheduler. Un acceso sin el header correcto devuelve **403**.

> **Importante**: el comando `weather:fetch` está cubierto por la misma configuración de cron-job.org sin ningún cambio adicional. Cuando cron-job.org llama al endpoint a las 06:00, Laravel ejecuta `weather:fetch` porque le toca según su schedule `dailyAt('06:00')`. En el resto de llamadas del día, el scheduler lo ignora automáticamente. No es necesario crear un cron job separado en cron-job.org para este comando.

---

## Tests automatizados

La suite de tests usa **PHPUnit** con **SQLite en memoria** como base de datos de pruebas, completamente aislada de la BD de desarrollo.

```bash
# Ejecutar todos los tests
docker exec -it padel-app php artisan test
```

Resultado actual: **51 tests pasados, 0 fallos, 114 assertions**.

### Archivos de test

```
tests/
├── Unit/
│   └── ExampleTest.php
└── Feature/
    ├── Auth/
    │   ├── AuthenticationTest.php
    │   ├── EmailVerificationTest.php
    │   ├── PasswordConfirmationTest.php
    │   ├── PasswordResetTest.php
    │   ├── PasswordUpdateTest.php
    │   └── RegistrationTest.php
    ├── AuthTest.php               # Login, logout, control de acceso por rol
    ├── ClassTest.php              # Clases: acceso, inscripción, duplicados
    ├── CourtTest.php              # Pistas: CRUD, desactivación con reservas
    ├── ProfileTest.php            # Perfil: renderizado, edición, borrado de cuenta
    ├── ReservationTest.php        # Reservas: acceso, cancelación, seguridad
    └── SchedulerEndpointTest.php  # Endpoint /run-scheduler: autenticación con header
```

Ver `docs/TEST_PLAN.md` para el plan de pruebas completo con casos manuales y automatizados.

---

## Estructura del repositorio

```
/
├── docker/
│   ├── nginx/          # Configuración Nginx (local y Railway)
│   ├── php/            # Dockerfile PHP-FPM y script de arranque
│   └── mysql/          # Datos persistentes MySQL (ignorados en git)
├── src/                # Código fuente Laravel
│   ├── app/
│   │   ├── Console/
│   │   │   └── Commands/
│   │   │       ├── CompleteFinishedClasses.php    # Scheduler: completar clases
│   │   │       ├── CompleteFinishedReservations.php # Scheduler: marcar reservas pagadas
│   │   │       ├── FetchWeatherData.php            # Scheduler: caché meteorológica (Open-Meteo)
│   │   │       ├── BackupDatabase.php              # Backup SQL de la BD → storage/app/backups/
│   │   │       └── RestoreDatabase.php             # Restauración desde archivo de backup
│   │   ├── Exports/            # ReservationsExport, RevenueExport
│   │   ├── Http/
│   │   │   ├── Controllers/    # Organizados por rol (Admin, Coach, Player)
│   │   │   └── Middleware/
│   │   │       └── CheckRole.php
│   │   ├── Mail/
│   │   │   └── BrevoTransport.php   # Transport HTTP personalizado para emails en Railway
│   │   ├── Models/             # User, Court, Reservation, PadelClass, ClassRegistration, Role, WeatherCache
│   │   └── Notifications/      # ClassRegistrationNotification, PublicClassNotification
│   ├── config/
│   │   └── padelsync.php       # Configuración propia del proyecto (cron_secret)
│   ├── database/
│   │   ├── factories/
│   │   │   └── UserFactory.php # Factory con estados por rol (admin, coach, player)
│   │   ├── migrations/
│   │   └── seeders/
│   ├── resources/
│   │   └── views/
│   │       ├── admin/          # courts/, users/, dashboard.blade.php
│   │       ├── coach/          # classes/
│   │       ├── player/         # reservations/, classes/
│   │       ├── livewire/       # auth/, layout/
│   │       ├── dashboard.blade.php   # Home autenticada con carrusel Alpine.js
│   │       ├── profile.blade.php
│   │       └── welcome.blade.php
│   ├── routes/
│   │   ├── web.php             # Incluye el endpoint /run-scheduler
│   │   ├── console.php         # Registro del scheduler (sin Kernel.php en Laravel 12)
│   │   └── auth.php
│   ├── storage/
│   │   └── app/
│   │       └── backups/        # Backups SQL generados por db:backup
│   └── tests/
│       └── Feature/            # Tests de integración por módulo
├── docs/
│   ├── ARCHITECTURE.md
│   ├── INSTALL.md
│   ├── USAGE.md
│   └── TEST_PLAN.md            # Plan de pruebas con casos y resultados
├── docker-compose.yml
├── README.md
├── DOCUMENTATION.md
└── BITACORA.md
```