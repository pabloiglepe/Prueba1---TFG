# Documentación Técnica — PadelSync

> Proyecto Final de Grado Superior DAW  
> Autor: Pablo Iglesias Peral   
> Curso: 2025/2026

---

## Índice

1. [Descripción del proyecto](#1-descripción-del-proyecto)
2. [Stack tecnológico](#2-stack-tecnológico)
3. [Base de datos](#3-base-de-datos)
4. [Sistema de roles y seguridad](#4-sistema-de-roles-y-seguridad)
5. [Módulos implementados](#5-módulos-implementados)
6. [Decisiones técnicas relevantes](#6-decisiones-técnicas-relevantes)
<!-- 7. [Bitácora de lecciones aprendidas](#7-bitácora-de-lecciones-aprendidas) -->

> Para la arquitectura del sistema consulta [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md)  
> Para la instalación consulta [docs/INSTALL.md](docs/INSTALL.md)  
> Para el manual de uso consulta [docs/USAGE.md](docs/USAGE.md)

---

## 1. Descripción del proyecto

**PadelSync** es una aplicación web para la gestión integral de un club de pádel. El sistema digitaliza la operativa diaria desde tres perspectivas:

- **Administración del centro**: gestión de pistas, usuarios y analíticas del negocio.
- **Gestión de la academia**: creación y organización de clases por parte de los entrenadores.
- **Experiencia del jugador**: reserva de pistas e inscripción a clases.

---

## 2. Stack tecnológico

| Capa | Tecnología | Versión |
|---|---|---|
| Backend | Laravel | 12.x |
| Frontend | Blade + Livewire Volt | — |
| Base de datos | MySQL | 8.0 |
| Servidor web | Nginx | Alpine |
| Runtime PHP | PHP-FPM | 8.2 |
| Compilación assets | Vite + Tailwind CSS | — |
| Gráficos | ECharts | 5.x (npm) |
| Iconos | Feather Icons | SVG inline |
| Exportación Excel | maatwebsite/excel | 3.x |
| Contenerización | Docker + Docker Compose | — |
| Despliegue | Railway | — |
| Control de versiones | Git + GitHub | — |

---

## 3. Base de datos

### Diagrama de tablas

```
roles ──────────────── users ──────────────── reservations
  id                    id                       id
  name                  role_id (FK)             user_id (FK)
                        name                     court_id (FK)
                        email                    reservation_date
                        password (bcrypt)        start_time
                        phone_number             end_time
                        rgpd_consent             total_price
                        deleted_at (softDelete)  status (pending/paid/cancelled)
                        timestamps               timestamps

courts ─────────────── classes ─────────────── classes_reservations
  id                    id                       id
  name                  coach_id (FK → users)    class_id (FK)
  type (cristal/muro)   court_id (FK)            user_id (FK)
  surface (cesped/      title                    status (registered/cancelled)
    cemento)            type (individual/group)  timestamps
  is_active             level
  timestamps            visibility (public/
                          private)
                        status (registered/
                          cancelled/completed)
                        date
                        start_time
                        end_time
                        max_players
                        price
                        timestamps

notifications (driver database de Laravel)
```

### Descripción de tablas

**`roles`** — Define los tres roles del sistema: `admin`, `coach`, `player`.

**`users`** — Usuarios del sistema. Incluye `softDeletes` para cumplimiento RGPD y campo `rgpd_consent` para el consentimiento de protección de datos.

**`courts`** — Pistas del club. Gestionadas por el administrador mediante CRUD completo. El campo `is_active` controla si la pista está disponible para reservas.

**`reservations`** — Reservas de pistas por parte de los jugadores. El campo `status` puede ser `pending`, `paid` o `cancelled`. Duración fija de 90 minutos con tarifa dinámica según la hora del día.

**`classes`** — Clases creadas por los entrenadores. Pueden ser `public` (el jugador se inscribe solo) o `private` (el entrenador inscribe directamente a los alumnos). La visibilidad no puede modificarse una vez creada la clase.

**`classes_reservations`** — Tabla pivote entre `classes` y `users`. Registra las inscripciones de jugadores a clases con su estado (`registered` / `cancelled`). Tiene restricción `unique(class_id, user_id)` para evitar inscripciones duplicadas.

**`notifications`** — Tabla generada por Laravel para el sistema de notificaciones con driver `database`.

---

## 4. Sistema de roles y seguridad

### Roles

| Rol | Prefijo de rutas | Descripción |
|---|---|---|
| `admin` | `/admin` | Control total de la aplicación |
| `coach` | `/coach` | Gestión de clases y alumnos |
| `player` | `/player` | Reservas e inscripción a clases |

> El rol `admin` tiene acceso también a las rutas de `coach` y `player`.

### Middleware `CheckRole`

Implementado en `app/Http/Middleware/CheckRole.php`. Protege las rutas verificando que el rol del usuario autenticado coincide con los roles permitidos:

```php
Route::middleware(['auth', 'role:admin'])->group(...);
Route::middleware(['auth', 'role:coach,admin'])->group(...);
Route::middleware(['auth', 'role:player,admin'])->group(...);
```

### Redirección por rol tras login

El `RedirectController` redirige al usuario a su panel correspondiente según su rol tras autenticarse:

```php
return match($request->user()->role->name) {
    'admin'  => redirect()->route('admin.dashboard'),
    'coach'  => redirect()->route('coach.classes.index'),
    'player' => redirect()->route('player.reservations.index'),
};
```

### Seguridad adicional

- **Bcrypt**: almacenamiento seguro de contraseñas mediante el cast `hashed` de Laravel.
- **CSRF**: protección nativa de Laravel en todos los formularios mediante `@csrf`.
- **SQL Injection**: prevenida mediante el ORM Eloquent (consultas parametrizadas).
- **SoftDeletes**: borrado lógico de usuarios para cumplimiento RGPD.
- **RGPD**: casilla de consentimiento obligatoria en el registro (`rgpd_consent`). Exportación de datos en JSON desde el perfil.

---

## 5. Módulos implementados

### 5.1 CRUD de Pistas (Admin)

**Controlador**: `App\Http\Controllers\Admin\CourtController`  
**Rutas**: `admin/courts`   
**Vistas**: `resources/views/admin/courts/`

Permite al administrador crear, editar, activar/desactivar y eliminar pistas. La vista de edición muestra estadísticas de la pista (reservas totales, ingresos y última reserva) y bloquea la desactivación si hay reservas futuras pendientes.

---

### 5.2 Motor de Reservas (Player)

**Controlador**: `App\Http\Controllers\Player\ReservationController`  
**Rutas**: `player/reservations`  
**Vistas**: `resources/views/player/reservations/`

#### Flujo de reserva

1. El jugador selecciona una **fecha**.
2. El sistema genera automáticamente las **franjas horarias disponibles** (09:00 - 22:00, cada 30 minutos), sin incluir las pistas ocupadas.
3. Al seleccionar una franja, el sistema muestra las **pistas libres** en ese horario.
4. El jugador elige pista y **confirma** la reserva.

#### Características técnicas

- **Duración fija**: 1 hora 30 minutos por reserva.
- **Anti-solapamiento**: doble validación (en búsqueda y en escritura) para evitar reservas duplicadas en la misma pista y horario.
- **Tarifa dinámica**: el precio varía según la hora del atardecer en Sevilla para cada mes del año, sin depender de APIs externas.

#### Tarifa nocturna por mes (Sevilla)

| Mes | Hora inicio tarifa nocturna |
|---|---|
| Enero, Diciembre | 18:00 |
| Febrero, Noviembre | 18:30 |
| Marzo, Octubre | 19:00 |
| Abril, Septiembre | 20:00 |
| Mayo, Julio, Agosto | 21:15 |
| Junio | 21:30 |

> Tarifa diurna: **12 €** · Tarifa nocturna: **16 €**

---

### 5.3 Dashboard de Analíticas (Admin)

**Controlador**: `App\Http\Controllers\Admin\DashboardController`  
**Ruta**: `admin/dashboard`  
**Vista**: `resources/views/admin/dashboard.blade.php`

El dashboard se organiza en tres pestañas: Resumen, Entrenadores y Exportar.

#### KPIs

| KPI | Descripción |
|---|---|
| Reservas totales | Total de reservas no canceladas |
| Ingresos totales | Suma de `total_price` de reservas no canceladas |
| Jugadores registrados | Total de usuarios con rol `player` |
| Jugadores activos | Players con al menos 1 reserva en los últimos 30 días |

#### Gráficos interactivos (ECharts)

- **Ocupación de pistas**: reservas por semana (4 semanas pasadas + 4 futuras). Al pulsar en un punto se abre un modal con el detalle de esa semana.
- **Ingresos por mes**: ingresos de los últimos 6 meses. Al pulsar en una barra se abre un modal con el desglose por pista y listado de reservas.

Los datos del gráfico se cargan desde atributos `data-` del HTML para evitar conflictos de redeclaración con Livewire Navigate.

#### Exportación Excel

Usando la librería `maatwebsite/excel`:
- **Reservas**: filtro por rango de fechas → `ReservationsExport.php`
- **Ingresos**: filtro por mes → `RevenueExport.php`

---

### 5.4 Panel del Entrenador

**Controlador**: `App\Http\Controllers\Coach\ClassController`  
**Rutas**: `coach/classes`   
**Vistas**: `resources/views/coach/classes/`

#### Flujo de creación de clase

1. Seleccionar **pista y fecha**.
2. Elegir **franja horaria** disponible (sin solapamiento con otras clases ni reservas).
3. Rellenar **datos**: título, tipo, nivel, visibilidad, plazas y precio.

#### Validación de solapamiento

El sistema valida que el horario elegido no se solape con:
- Otras clases en la misma pista.
- Reservas de jugadores en la misma pista.

Esta validación se aplica tanto en la creación (`store`) como en la edición (`update`), sin encluir la propia clase en el caso de la edición.

#### Sistema de notificaciones

- **Clase pública**: notificación automática a todos los jugadores al crearla (`PublicClassNotification`).
- **Clase privada**: notificación individual a cada alumno inscrito (`ClassRegistrationNotification`).

> El sistema está preparado para activar el canal `mail` en el futuro. Actualmente solo usa el driver `database`.

---

### 5.5 Panel del Jugador

**Controladores**: `Player\ReservationController`, `Player\ClassController`  
**Vistas**: `resources/views/player/`

- **Reservas**: listado con opción de cancelar. Las reservas canceladas muestran el estado pero no permiten más acciones.
- **Clases**: dos secciones — clases inscritas (con opción de cancelar inscripción si la clase es futura) y clases públicas disponibles con plazas libres.

---

### 5.6 Gestión de Usuarios (Admin)

**Controlador**: `App\Http\Controllers\Admin\UserController`  
**Rutas**: `admin/users`   
**Vistas**: `resources/views/admin/users/`

- Listado separado por tabs (Jugadores / Entrenadores) con buscador integrado y avatares con iniciales.
- Vista de edición con estadísticas diferenciadas por rol.
- El email no puede modificarse desde la vista de admin.

---

### 5.7 Perfil de usuario

**Controlador**: `App\Http\Controllers\ProfileController`  
**Rutas**: `profile` 

- Edición de nombre y teléfono.
- Cambio de contraseña con verificación de la actual.
- Exportación de datos en JSON (RGPD).
- Borrado lógico de cuenta con cancelación de reservas pendientes.
- Tarjetas de estadísticas diferenciadas por rol.
- Historial de reservas y clases (solo player).
- Listado de clases creadas con ingresos (solo coach).

---

## 6. Decisiones técnicas relevantes

### `PadelClass` en lugar de `Class`
`Class` es una palabra reservada en PHP. El modelo se llama `PadelClass` con `$table = 'classes'` para mantener el nombre de tabla correcto en la base de datos.

### ECharts via npm, no CDN
ECharts se instala como dependencia npm (`import * as echarts from 'echarts'`) y se expone globalmente en `app.js` con `window.echarts = echarts`. Esto evita dependencias externas y permite que la app sea más óptima.

### Datos de gráficos en atributos `data-`
Los datos PHP para ECharts se pasan a través de atributos `data-` del HTML con `htmlspecialchars(json_encode(...), ENT_NOQUOTES)`. Esto evita el error de redeclaración de variables `const` al navegar con Livewire Navigate.

### Tarifa nocturna sin API externa
Se usa una tabla de atardeceres aproximados por mes para Sevilla en lugar de consumir una API externa. Esto hace la aplicación más robusta al no depender de servicios de terceros.

### Doble validación anti-solapamiento
Las reservas y clases validan la disponibilidad tanto en la búsqueda (para mostrar solo franjas libres) como en el momento de escritura en base de datos. Esto previene condiciones de carrera si dos usuarios reservan simultáneamente.

### Visibilidad de clase inmutable
Una vez creada una clase, su visibilidad (`public` / `private`) no puede modificarse. Esto previene inconsistencias con notificaciones ya enviadas e inscripciones existentes. En el controlador se ignora el campo `visibility` del formulario de edición y se mantiene el valor original.

### Livewire Volt para autenticación
Se optó por Livewire Volt  para mantener toda la lógica reactiva dentro del ecosistema Laravel, simplificando el despliegue.

### Iconos SVG inline (Feather Icons)
Los iconos se incluyen como SVG inline sin dependencias externas. Esto evita peticiones HTTP adicionales y permite controlar el tamaño y color directamente con CSS.

---

<!-- ## 7. Bitácora de lecciones aprendidas

### Hito 1 — Configuración del entorno Docker
**Problema**: tras levantar los contenedores, Laravel mostraba error 500 por permisos denegados en `storage` y `bootstrap/cache`.  
**Solución**: dar permisos de escritura a estas carpetas tras instalar el proyecto.

### Hito 2 — Recompilación de assets Tailwind
**Problema**: las clases de Tailwind añadidas en vistas nuevas no se aplicaban.  
**Causa**: Tailwind en modo producción solo incluye las clases detectadas en el último build.  
**Solución**: ejecutar `npm run build` cada vez que se añadan clases nuevas.

### Hito 3 — Helper `auth()` no reconocido por el IDE
**Problema**: el IDE marcaba `auth()->user()` como no encontrado.  
**Solución**: usar `$request->user()` en su lugar, que es más semánticamente correcto.

### Hito 4 — Error SQLSTATE en campo `status`
**Problema**: MySQL devolvía `Data truncated for column 'status'` al crear una reserva.  
**Causa**: la migración definía el enum en español pero el controlador insertaba valores en inglés.  
**Solución**: crear una nueva migración con `->change()` para actualizar los valores del enum. Nunca modificar migraciones ya ejecutadas directamente.

### Hito 5 — Modal del dashboard sin posicionamiento correcto
**Problema**: el modal no flotaba como overlay sino que se insertaba en el flujo de la página.  
**Causa**: el layout de Laravel tiene `overflow` que rompe el `position: fixed` de Tailwind.  
**Solución**: usar estilos CSS inline con `position:fixed` y `z-index:9999`.

### Hito 6 — Redeclaración de variables con Livewire Navigate
**Problema**: al navegar entre páginas con Livewire Navigate, el script del dashboard lanzaba `Identifier 'occupancyLabels' has already been declared`.  
**Causa**: Livewire Navigate no recarga la página completa, por lo que el script se ejecuta de nuevo pero las variables `const` no pueden redeclararse.  
**Solución**: mover los datos PHP a atributos `data-` del HTML y leerlos desde JavaScript mediante `element.dataset`.

### Hito 7 — ECharts no renderizaba el gráfico de líneas
**Problema**: el gráfico de ocupación mostraba los ejes pero no la línea de datos.  
**Causa**: ECharts inicializa con las dimensiones del contenedor en ese momento. El tab de Alpine.js tenía `x-show` que ocultaba el contenedor con `display:none`, haciendo que las dimensiones fueran cero al inicializar.  
**Solución**: añadir un `setTimeout` de 50ms tras inicializar para forzar `chartOccupancy.resize()`.

### Hito 8 — Gráfico de ocupación sin datos
**Problema**: el gráfico de líneas mostraba todos los valores a 0 aunque había reservas.  
**Causa**: el gráfico mostraba las últimas 8 semanas pasadas, pero las reservas de prueba eran de semanas futuras.  
**Solución**: cambiar el rango del bucle de `-7..0` a `-4..+3` para mostrar 4 semanas pasadas y 4 futuras.

### Hito 9 — Campo `type` truncado en clases (grupal vs group)
**Problema**: al guardar una clase grupal, MySQL devolvía `Data truncated for column 'type'`.  
**Causa**: el enum de la tabla `classes` tenía el valor `group` pero el formulario enviaba `grupal`.  
**Solución**: unificar la nomenclatura en inglés (`group`) en formularios, vistas y validaciones del controlador.

### Hito 10 — Campos `disabled` no se envían en el formulario
**Problema**: al crear una clase individual, el campo `max_players` deshabilitado no se enviaba y la validación fallaba silenciosamente.  
**Causa**: los campos HTML con atributo `disabled` no se incluyen en el submit del formulario.  
**Solución**: añadir un `<input type="hidden" name="max_players" id="max_players_hidden">` que siempre se envía, y sincronizarlo con el input visible mediante `oninput` y Alpine.js. -->