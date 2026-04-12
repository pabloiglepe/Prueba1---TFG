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
| Iconos | Feather Icons | SVG inline |
| Contenerización | Docker + Docker Compose | — |
| Despliegue | Railway | — |
| Control de versiones | Git + GitHub | — |

---

## Contenedores Docker

La aplicación se divide en cuatro servicios interconectados mediante la red interna `padel-network`:

```
┌─────────────────────────────────────────────┐
│                padel-network                │
│                                             │
│  ┌──────────┐    ┌──────────┐    ┌────────┐ │
│  │  Nginx   │───▶│ PHP-FPM  │───▶│ MySQL │ │
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
| `padel-node` | Node.js 20 | Compilación de assets con Vite |

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

## Estructura del repositorio

```
/
├── docker/
│   ├── nginx/          # Configuración Nginx (local y Railway)
│   ├── php/            # Dockerfile PHP-FPM y script de arranque
│   └── mysql/          # Datos persistentes MySQL (ignorados en git)
├── src/                # Código fuente Laravel
│   ├── app/
│   │   ├── Exports/            # ReservationsExport, RevenueExport -> Controladores de exportación de datos
│   │   ├── Http/
│   │   │   ├── Controllers/    # Organizados por rol (Admin, Coach, Player)
│   │   │   └── Middleware/
│   │   │       └── CheckRole.php
│   │   ├── Models/             # User, Court, Reservation, PadelClass, ClassRegistration, Role
│   │   └── Notifications/      # ClassRegistrationNotification, PublicClassNotification
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── resources/
│   │   └── views/
│   │       ├── admin/          # courts/, users/, dashboard.blade.php
│   │       ├── coach/          # classes/
│   │       ├── player/         # reservations/, classes/
│   │       ├── livewire/       # auth/, layout/
│   │       ├── profile.blade.php
│   │       └── welcome.blade.php
│   └── routes/
│       ├── web.php
│       └── auth.php
├── docs/               # Documentación dividida por secciones
├── docker-compose.yml
├── README.md
├── DOCUMENTATION.md
└── BITACORA.md
```
