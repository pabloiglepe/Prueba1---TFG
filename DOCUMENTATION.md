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
| Backend | Laravel | 12.0 |
| Frontend | Blade + Livewire Volt | — |
| Base de datos | MySQL | 8.0 |
| Servidor web | Nginx | Alpine |
| Runtime PHP | PHP-FPM | 8.2 |
| Compilación assets | Vite + Tailwind CSS | — |
| Gráficos | ECharts | 5.6.0 (npm) |
| Iconos | Heroicons / FontAwesome / iconify-icon | SVG inline / npm |
| Alertas | SweetAlert2 | CDN |
| Exportación Excel | maatwebsite/excel | 3.1 |
| Datos meteorológicos | Open-Meteo API | Gratuita, sin API key |
| Envío de emails | Brevo API HTTP | — |
| Contenerización | Docker + Docker Compose | — |
| Despliegue | Railway | — |
| Scheduler (producción) | cron-job.org | — |
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
  is_outdoor            visibility (public/
  timestamps              private)
                        status (registered/
                          cancelled/completed)
                        date
                        start_time
                        end_time
                        max_players
                        price
                        timestamps

notifications            password_reset_tokens    weather_cache
(driver database         (nativo de Laravel)        date (PK)
de Laravel)                                         sunrise
                                                    sunset
                                                    precipitation_mm
                                                    fetched_at
```

### Descripción de tablas

**`roles`** — Define los tres roles del sistema: `admin`, `coach`, `player`.

**`users`** — Usuarios del sistema. Incluye `softDeletes` para cumplimiento RGPD y campo `rgpd_consent` para el consentimiento de protección de datos.

**`courts`** — Pistas del club. Gestionadas por el administrador mediante CRUD completo. El campo `is_active` controla si la pista está disponible para reservas. El campo `is_outdoor` indica si la pista es exterior; las pistas exteriores se bloquean automáticamente cuando hay lluvia prevista.

**`reservations`** — Reservas de pistas por parte de los jugadores. El campo `status` puede ser `pending`, `paid` o `cancelled`. Duración fija de 90 minutos con tarifa dinámica según la hora del día.

**`classes`** — Clases creadas por los entrenadores. Pueden ser `public` (el jugador se inscribe solo) o `private` (el entrenador inscribe directamente a los alumnos). La visibilidad no puede modificarse una vez creada la clase.

**`classes_reservations`** — Tabla pivote entre `classes` y `users`. Registra las inscripciones de jugadores a clases con su estado (`registered` / `cancelled`). Tiene restricción `unique(class_id, user_id)` para evitar inscripciones duplicadas.

**`weather_cache`** — Caché local de datos meteorológicos obtenidos de Open-Meteo. Almacena un registro por día con la hora de amanecer (`sunrise`), ocaso (`sunset`) y precipitación prevista (`precipitation_mm`). El comando `weather:fetch` la rellena diariamente con los próximos 14 días. Las vistas la consultan para calcular la tarifa nocturna real y bloquear pistas exteriores cuando llueve.

**`notifications`** — Tabla generada por Laravel para el sistema de notificaciones con driver `database`.

**`password_reset_tokens`** — Tabla nativa de Laravel para almacenar los tokens temporales de recuperación de contraseña. Los tokens expiran tras 60 minutos.

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
- **Recuperación de contraseña**: tokens firmados con expiración de 60 minutos enviados por email mediante Brevo API HTTP.

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
2. Si hay lluvia prevista (`precipitation_mm >= 1 mm`), se muestra un aviso informativo y las pistas exteriores quedan excluidas.
3. El sistema genera automáticamente las **franjas horarias disponibles** (09:00 - 22:00, cada 30 minutos), excluyendo las ocupadas.
4. Al seleccionar una franja, el sistema muestra las **pistas libres** en ese horario.
5. El jugador elige pista y **confirma** la reserva.

#### Características técnicas

- **Duración fija**: 1 hora 30 minutos por reserva.
- **Tarifa dinámica**: diurna (12 €) o nocturna (16 €) según la hora de inicio comparada con la hora de ocaso real del día obtenida de `weather_cache`. Si no hay dato en caché, se aplica un fallback estático por mes.
- **Bloqueo por lluvia**: si `weather_cache.precipitation_mm >= 1.0` para la fecha seleccionada, las pistas con `is_outdoor = true` no aparecen disponibles. La validación se aplica tanto en la vista como en el servidor.

> Tarifa diurna: **12 €** · Tarifa nocturna: **16 €**  
> Fuente del ocaso: **Open-Meteo API** vía tabla `weather_cache` (actualizada diariamente a las 06:00)

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

1. Seleccionar **fecha**: si hay lluvia prevista, se muestra un aviso y las pistas exteriores quedan excluidas del selector.
2. Seleccionar **pista** disponible para esa fecha.
3. Elegir **franja horaria** disponible (sin solapamiento con otras clases ni reservas).
4. Rellenar **datos**: título, tipo, nivel, visibilidad, plazas y precio.

#### Validación de solapamiento

El sistema valida que el horario elegido no se solape con:
- Otras clases en la misma pista.
- Reservas de jugadores en la misma pista.

Esta validación se aplica tanto en la creación (`store`) como en la edición (`update`), excluyendo la propia clase en el caso de la edición.

#### Sistema de notificaciones

- **Clase pública**: notificación automática a todos los jugadores al crearla (`PublicClassNotification`).
- **Clase privada**: notificación individual a cada alumno inscrito (`ClassRegistrationNotification`).

---

### 5.5 Panel del Jugador

**Controladores**: `Player\ReservationController`, `Player\ClassController`  
**Vistas**: `resources/views/player/`

- **Reservas**: listado con opción de cancelar. Las reservas canceladas muestran el estado pero no permiten más acciones.
- **Clases**: dos secciones — clases inscritas (con opción de cancelar inscripción si la clase es futura) y clases públicas disponibles con plazas libres.
- **Reinscripción**: si un jugador cancela su inscripción y quiere volver a inscribirse, el sistema actualiza el registro existente en lugar de crear uno nuevo, evitando el error de clave duplicada en `classes_reservations`.

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

El perfil se organiza en dos pestañas:

**Mi Perfil**
- Tarjetas de estadísticas diferenciadas por rol (gastos para player, clases e ingresos para coach).
- Edición de nombre y teléfono. Email y rol no son modificables.
- Exportación de datos en JSON (RGPD).
- Historial de reservas y clases inscritas (solo player).
- Listado de clases creadas con alumnos e ingresos (solo coach).

**Seguridad**
- Cambio de contraseña con verificación de la contraseña actual.
- Zona de peligro: borrado lógico de cuenta con cancelación de reservas pendientes.

---

### 5.8 Recuperación de contraseña

**Vistas**: `resources/views/livewire/pages/auth/`
- `forgot-password.blade.php` — Formulario para solicitar el enlace de recuperación.
- `reset-password.blade.php` — Formulario para establecer la nueva contraseña.

#### Flujo completo

1. El usuario accede a **¿Olvidaste la contraseña?** desde el login.
2. Introduce su email y el sistema genera un token firmado almacenado en `password_reset_tokens`.
3. Laravel envía un email real al usuario mediante **Brevo API HTTP** con un enlace que incluye el token.
4. El usuario pulsa el enlace, accede al formulario de reset y establece una nueva contraseña.
5. El token se invalida y el usuario es redirigido al login.

#### Configuración del transporte de email (Brevo API HTTP)

Railway bloquea las conexiones SMTP salientes, por lo que se implementó un `BrevoTransport` personalizado que usa la API HTTP de Brevo en lugar de SMTP:

```php
// app/Mail/BrevoTransport.php
protected function doSend(SentMessage $message): void
{
    Http::withHeaders(['api-key' => $this->apiKey])
        ->post('https://api.brevo.com/v3/smtp/email', $payload);
}
```

Registrado en `AppServiceProvider`:

```php
Mail::extend('brevo', fn() => new BrevoTransport(config('services.brevo.key')));
```

Variables de entorno necesarias:

```env
MAIL_MAILER=brevo
BREVO_API_KEY=tu_api_key
MAIL_FROM_ADDRESS=cuenta@gmail.com
MAIL_FROM_NAME="PadelSync"
```

> En local funciona también con SMTP de Brevo (`smtp-relay.brevo.com:587`), pero en Railway es necesario usar la API HTTP.

---

### 5.9 Scheduler — Tareas programadas

**Comandos Artisan**: definidos en `routes/console.php`  
**Ruta del endpoint**: `GET /run-scheduler`

El sistema tiene tres comandos Artisan registrados en el scheduler:

| Comando | Acción | Frecuencia |
|---|---|---|
| `classes:complete-finished` | Marca como `completed` las clases cuya hora de fin ha pasado | Cada 15 min |
| `reservations:mark-paid` | Marca como `paid` las reservas pasadas en estado `pending` | Cada 15 min |
| `weather:fetch` | Obtiene datos meteorológicos de Open-Meteo para los próximos 14 días | Diaria (06:00) |

#### Ejecución en local

```bash
docker exec -it padel-app php artisan schedule:run
```

#### Ejecución en producción (Railway + cron-job.org)

Railway no ofrece cron jobs nativos en el plan gratuito. La solución implementada es un **endpoint HTTP protegido** que dispara el scheduler, llamado periódicamente por el servicio externo **cron-job.org**:

```php
// routes/web.php
Route::get('/run-scheduler', function () {
    if (request()->header('X-Cron-Secret') !== config('padelsync.cron_secret')) {
        abort(403);
    }
    Artisan::call('schedule:run');
    return response('OK', 200);
});
```

**Configuración del endpoint**:

| Parámetro | Valor |
|---|---|
| URL | `https://prueba1-tfg-production.up.railway.app/run-scheduler` |
| Método | GET |
| Header de autenticación | `X-Cron-Secret: PadelsyncTfg123` |
| Variable de entorno Railway | `CRON_SECRET=PadelsyncTfg123` |

> Acceder al endpoint desde el navegador devuelve **403** — es el comportamiento correcto, ya que el header `X-Cron-Secret` no está presente.

> **Nota sobre `weather:fetch`**: este comando está cubierto por la misma configuración de cron-job.org sin ningún cambio adicional. cron-job.org llama al endpoint cada 15 minutos y Laravel decide qué comandos ejecutar según su schedule. Cuando la llamada coincide con las 06:00, Laravel ejecuta `weather:fetch`; el resto del día lo omite automáticamente. No se necesita un cron job adicional en cron-job.org.

---

### 5.10 Home autenticada con carrusel Alpine.js

**Vista**: `resources/views/dashboard.blade.php`  
**Ruta**: `/dashboard`

Tras el login, todos los roles acceden a una página de bienvenida unificada que presenta un **carrusel de slides adaptado al rol del usuario**. Cada slide incluye imagen de pádel, título, descripción y botón de acceso rápido.

#### Slides por rol

| Rol | Slides |
|---|---|
| Admin | Bienvenida · Acceso al Dashboard · Gestión de pistas · Gestión de usuarios |
| Coach | Bienvenida · Mis clases · Crear nueva clase |
| Player | Bienvenida · Reservar pista · Mis clases · Mi perfil |

#### Implementación técnica

- **Librería**: Alpine.js (incluido con Livewire, sin dependencias adicionales).
- **Navegación**: puntos de posición y flechas anterior/siguiente.
- **Imágenes**: fotografías reales de pádel servidas desde `public/images/`.
- **Adaptación por rol**: las slides se renderizan condicionalmente con `@if (auth()->user()->role->name === '...')` en la vista Blade.

---

### 5.11 UX de autenticación
 
**Vistas afectadas**:
- `resources/views/livewire/auth/login.blade.php`
- `resources/views/livewire/auth/register.blade.php`
- `resources/views/livewire/auth/forgot-password.blade.php`
- `resources/views/livewire/auth/reset-password.blade.php`
- `resources/views/welcome.blade.php`


Se realizó un pase de mejora sobre todas las vistas del flujo de autenticación aplicando tres capas de mejora de UX:
 
**Iconos Phosphor en campos y botones**  
Los campos de formulario (email, contraseña, nombre, teléfono) y los botones de acción incorporan iconos de la colección **Phosphor** (`ph:`) de iconify-icon como prefijo visual. Los iconos se sirven desde el bundle de Vite sin peticiones externas.
 
**Spinner de carga `padel-spin`**  
La animación CSS `padel-spin` se define en ambos layouts y se aplica en los botones de submit mediante `wire:loading`:
 
```css
@keyframes padel-spin {
    0%   { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.padel-spin {
    animation: padel-spin 0.7s linear infinite;
}
```
 
El spinner sustituye al texto del botón mientras Livewire procesa la petición, previniendo envíos duplicados y comunicando el estado al usuario.
 
**Mensajes de estado**  
Errores de validación inline con `@error`, confirmación de envío en forgot-password y mensajes de error en reset-password, siguiendo el sistema de feedback ya establecido en el resto de la aplicación.
 
---

### 5.12 Datos meteorológicos — Open-Meteo y WeatherCache

**Modelo**: `App\Models\WeatherCache`  
**Comando**: `app/Console/Commands/FetchWeatherData.php` (`weather:fetch`)  
**Tabla**: `weather_cache`

#### Fuente de datos

Se usa la API pública **Open-Meteo** (`https://api.open-meteo.com/v1/forecast`), gratuita, sin API key y sin registro. Una sola llamada devuelve 14 días de datos de amanecer, ocaso y precipitación:

```
?latitude=37.39&longitude=-5.99
&daily=sunrise,sunset,precipitation_sum
&timezone=Europe/Madrid
&forecast_days=14
```

#### Estrategia de caché

El comando `weather:fetch` se ejecuta **una vez al día a las 06:00** y actualiza `weather_cache` con los 14 días siguientes. Las vistas y controladores consultan únicamente la tabla local, sin ninguna llamada directa a la API en tiempo de petición.

Si un día no tiene registro en `weather_cache` (por ejemplo, porque el comando todavía no ha corrido tras instalar el proyecto), la lógica de precios cae al **fallback estático** de atardeceres por mes, garantizando que la aplicación siga funcionando.

#### Umbral de lluvia

```php
// app/Models/WeatherCache.php
const RAIN_THRESHOLD_MM = 1.0;
```

Cuando `precipitation_mm >= 1.0`, la propiedad `isRainy()` devuelve `true` y las pistas exteriores (`is_outdoor = true`) se excluyen de la selección tanto en reservas como en clases.

---

### 5.13 Plan de pruebas — Tests PHPUnit

**Archivos de test**: `tests/Feature/`  
**Documento de resultados**: `docs/TEST_PLAN.md`

#### Estrategia

Se combinan tests automatizados con PHPUnit y una lista de comprobación manual para los flujos que dependen de renderizado visual o servicios externos (ECharts, Alpine.js, email real).

La base de datos de tests usa **SQLite en memoria** configurada en `phpunit.xml`, completamente aislada de la BD de desarrollo. Cada test usa el trait `RefreshDatabase` para partir de un estado limpio.

#### Tests automatizados creados

| Archivo | Qué cubre |
|---|---|
| `AuthTest.php` | Login, logout, acceso por rol (8 tests) |
| `CourtTest.php` | CRUD de pistas, desactivación con reservas futuras (5 tests) |
| `ReservationTest.php` | Acceso, cancelación propia, seguridad entre usuarios (4 tests) |
| `ClassTest.php` | Acceso por rol, inscripción, inscripción duplicada (5 tests) |
| `SchedulerEndpointTest.php` | Autenticación del endpoint `/run-scheduler` (3 tests) |

Los tests de Breeze existentes (`Auth/*`, `ProfileTest`) se adaptaron al flujo personalizado de PadelSync.

#### Resultado de ejecución

```
Tests:    51 passed (114 assertions)
Duration: 33.02s
```

#### `UserFactory` con estados por rol

La `UserFactory` se actualizó para incluir estados por rol y un rol `player` por defecto:

```php
User::factory()->admin()->create();
User::factory()->coach()->create();
User::factory()->player()->create();
User::factory()->create(); // player por defecto
```

El rol por defecto evita el `NOT NULL constraint failed: users.role_id` en los tests de Breeze que usan `User::factory()` sin estado explícito.

---

### 5.14 Sistema de backup y restauración

**Comandos**: `app/Console/Commands/BackupDatabase.php`, `app/Console/Commands/RestoreDatabase.php`  
**Directorio de backups**: `storage/app/backups/`

#### `php artisan db:backup`

Genera un volcado SQL completo de la base de datos sin depender de `mysqldump` (no disponible en contenedores PHP-FPM estándar ni en Railway). Usa Laravel DB directamente:

- Obtiene el DDL de cada tabla con `SHOW CREATE TABLE`.
- Exporta las filas en bloques de 100 INSERT para evitar sentencias demasiado largas.
- Excluye tablas de datos volátiles: `cache`, `sessions`, `jobs`, `failed_jobs` y similares.
- Conserva los últimos 7 backups y elimina los más antiguos automáticamente.
- Acepta `--path=` para especificar una ruta de destino diferente.

```bash
docker exec -it padel-app php artisan db:backup
```

#### `php artisan db:restore`

Restaura la BD desde un archivo de backup generado por `db:backup`:

- Sin argumento: restaura el backup más reciente disponible.
- Con argumento `{file}`: restaura el archivo especificado por nombre.
- Con `--force`: omite la confirmación interactiva.
- Desactiva `FOREIGN_KEY_CHECKS` durante la restauración (y los reactiva siempre, incluso en caso de error).

```bash
docker exec -it padel-app php artisan db:restore
docker exec -it padel-app php artisan db:restore padelsync_backup_2026-05-03_191746.sql
```

#### Backup automático en el scheduler

```php
// routes/console.php — backup automático cada domingo a las 03:00
app(Schedule::class)->command('db:backup')->weeklyOn(0, '03:00');
```

En producción, cron-job.org llama al endpoint `/run-scheduler` y Laravel ejecuta el backup cuando le corresponde según el schedule.

#### Backup del código fuente

El código fuente no requiere un sistema adicional: **Git + GitHub** actúa como backup versionado. Cada `git push` es un backup completo del código. `db:backup` cubre exclusivamente los **datos de la BD** que no están en el repositorio.

## 6. Decisiones técnicas relevantes

### `PadelClass` en lugar de `Class`
`Class` es una palabra reservada en PHP. El modelo se llama `PadelClass` con `$table = 'classes'` para mantener el nombre de tabla correcto en la base de datos.

### ECharts via npm, no CDN
ECharts se instala como dependencia npm (`import * as echarts from 'echarts'`) y se expone globalmente en `app.js` con `window.echarts = echarts`. Esto evita dependencias externas y permite que la app sea más óptima.

### iconify-icon via npm, no CDN
La librería de iconos **iconify-icon** se instala vía npm en el contenedor `padel-node` para evitar peticiones externas en tiempo de carga y garantizar disponibilidad sin conexión. Proporciona acceso a más de 200.000 iconos de múltiples colecciones (Material Design, Tabler, Phosphor, etc.) y se usa como web component en las vistas Blade.

### Datos de gráficos en atributos `data-`
Los datos PHP para ECharts se pasan a través de atributos `data-` del HTML con `htmlspecialchars(json_encode(...), ENT_NOQUOTES)`. Esto evita el error de redeclaración de variables `const` al navegar con Livewire Navigate.

### Open-Meteo para tarifa nocturna real y bloqueo outdoor
Se usa la API pública Open-Meteo (gratuita, sin API key) para obtener la hora de ocaso real de cada día y la precipitación prevista. Los datos se almacenan en la tabla `weather_cache` mediante el comando `weather:fetch`, que corre una vez al día a las 06:00. Las vistas consultan únicamente la tabla local, con un fallback estático por mes si no hay dato en caché. Esto hace la lógica de precios más precisa y añade el bloqueo automático de pistas exteriores cuando llueve.

### Doble validación anti-solapamiento
Las reservas y clases validan la disponibilidad tanto en la búsqueda (para mostrar solo franjas libres) como en el momento de escritura en base de datos. Esto previene condiciones de carrera si dos usuarios reservan simultáneamente.

### Visibilidad de clase inmutable
Una vez creada una clase, su visibilidad (`public` / `private`) no puede modificarse. Esto previene inconsistencias con notificaciones ya enviadas e inscripciones existentes. En el controlador se ignora el campo `visibility` del formulario de edición y se mantiene el valor original.

### Reinscripción mediante búsqueda y actualización
La tabla `classes_reservations` tiene una restricción `unique(class_id, user_id)`. Para permitir que un jugador se reinscriba tras cancelar, el controlador busca el registro existente y actualiza su estado a `registered` en lugar de intentar crear uno nuevo, evitando la violación de la clave única.

### Brevo API HTTP para emails en producción
Railway bloquea los puertos SMTP salientes (25, 465, 587). Para solucionar esto se implementó un transport personalizado que usa la API HTTP de Brevo, que funciona sobre HTTPS (puerto 443, siempre abierto). En local se puede usar SMTP de Brevo directamente.

### Livewire Volt para autenticación
Se optó por Livewire Volt para mantener toda la lógica reactiva dentro del ecosistema Laravel, simplificando el despliegue.

### Scheduler en producción via endpoint HTTP protegido
Railway no soporta cron jobs nativos en el plan gratuito. Se implementó un endpoint `GET /run-scheduler` protegido con el header `X-Cron-Secret` que dispara `php artisan schedule:run`. El servicio externo **cron-job.org** llama a este endpoint cada 30 minutos, simulando el comportamiento de un cron job.

### Home autenticada con carrusel Alpine.js
En lugar de redirigir directamente al panel de rol tras el login, se creó una home unificada en `/dashboard` con un carrusel de accesos rápidos adaptado al rol. Esto mejora la orientación del usuario y centraliza el punto de entrada a la aplicación.

### Spinner `padel-spin` definido en los layouts
La animación de carga se define una única vez en `app.blade.php` y `guest.blade.php`, garantizando disponibilidad en todas las vistas sin duplicar código. Se activa con `wire:loading` en los botones de submit de los formularios de autenticación para prevenir envíos duplicados y dar feedback inmediato al usuario.

### `config/padelsync.php` para configuración propia del proyecto
La configuración específica de PadelSync (como `cron_secret`) se centraliza en `config/padelsync.php` en lugar de llamar a `env()` directamente en rutas o controladores. Esto sigue la práctica recomendada de Laravel y permite sobreescribir valores en tests con `config([...])`, algo que `env()` no permite en runtime.

### Tests automatizados con UserFactory por roles
La `UserFactory` incluye estados `->admin()`, `->coach()`, `->player()` para crear usuarios con el rol correcto en los tests. El rol `player` es el estado base del `definition()` para que los tests de Breeze funcionen sin estado explícito.