# Bitácora de Desarrollo — PadelSync

> Registro de problemas encontrados, decisiones tomadas y lecciones aprendidas durante el desarrollo del proyecto.  
> Autor: Pablo Iglesias Peral · Curso 2025/2026

---

## Hito 1 — Configuración del entorno Docker

**Problema**: tras levantar los contenedores, Laravel mostraba error 500 por permisos denegados en `storage` y `bootstrap/cache`.  
**Causa**: Docker crea los directorios con el usuario `root` y PHP-FPM no tiene permisos de escritura.  
**Solución**: dar permisos de escritura a estas carpetas tras instalar el proyecto:

```bash
docker exec -it padel-app chmod -R 775 storage bootstrap/cache
```

**Lección**: siempre verificar permisos de carpetas de Laravel tras levantar un entorno Docker por primera vez.

---

## Hito 2 — Recompilación de assets Tailwind

**Problema**: las clases de Tailwind añadidas en vistas nuevas no se aplicaban en el navegador.  
**Causa**: Tailwind en modo producción genera un CSS con solo las clases detectadas en el último `build`. Las clases nuevas no existen hasta el siguiente build.  
**Solución**: ejecutar `npm run build` cada vez que se añadan clases nuevas en las vistas.

```bash
docker exec -it padel-node npm run build
```

**Lección**: en desarrollo es preferible usar `npm run dev` para que Vite recargue automáticamente. Usar `npm run build` solo para producción o Railway.

---

## Hito 3 — Helper `auth()` no reconocido por el IDE

**Problema**: el IDE marcaba `auth()->check()` y `auth()->user()` como métodos no encontrados.  
**Causa**: `auth()` es un helper global de Laravel que el analizador estático del IDE no puede resolver sin plugins específicos.  
**Solución**: usar `$request->user()` en lugar de `auth()->user()` dentro de los controladores.

**Lección**: elegí la inyección de dependencias (`Request $request`) sobre helpers globales para mejorar la legibilidad y el soporte del IDE.

---

## Hito 4 — Error SQLSTATE en campo `status` (enum)

**Problema**: al crear una reserva, MySQL devolvía `Data truncated for column 'status'`.  
**Causa**: la migración definía el enum con valores en español (`pendiente`, `pagada`, `cancelada`) pero el controlador insertaba valores en inglés (`pending`, `paid`, `cancelled`).  
**Solución**: crear una nueva migración con `->change()` para actualizar los valores del enum. Nunca modificar migraciones ya ejecutadas directamente.

**Lección**: definir desde el principio los valores de los enums en el idioma que se va a usar en el código (inglés). Nunca editar una migración ya ejecutada, siempre crear una nueva.

---

## Hito 5 — Modal del dashboard sin posicionamiento correcto

**Problema**: el modal de detalle de reservas no flotaba como overlay sino que se insertaba en el flujo de la página, desplazando el contenido.  
**Causa**: el layout principal de Laravel tiene propiedades `overflow` que rompen el `position: fixed` de las clases de Tailwind.  
**Solución**: usar estilos CSS inline con `position: fixed`, `inset: 0` y `z-index: 9999` en lugar de depender de las clases utilitarias de Tailwind para el posicionamiento del modal.

**Lección**: para elementos que deben flotar sobre toda la página (modales, overlays), preferir CSS inline con `position: fixed` para evitar que el contexto de apilamiento del layout interfiera.

---

## Hito 6 — Redeclaración de variables con Livewire Navigate

**Problema**: al navegar entre páginas con Livewire Navigate, el script del dashboard lanzaba `Uncaught SyntaxError: Identifier 'occupancyLabels' has already been declared`.  
**Causa**: Livewire Navigate no recarga la página completa, solo sustituye el contenido del DOM. El bloque `<script>` se ejecuta de nuevo pero las variables declaradas con `const` no pueden redeclararse en el mismo contexto.  
**Solución**: mover los datos PHP a atributos `data-` del HTML usando `htmlspecialchars(json_encode(...), ENT_NOQUOTES)` y leerlos desde JavaScript mediante `element.dataset` dentro del evento `livewire:navigated`.

```html
<div id="dashboard-data"
     data-occupancy-labels="{{ htmlspecialchars(json_encode($occupancyLabels), ENT_NOQUOTES) }}"
     ...>
</div>
```

```javascript
document.addEventListener('livewire:navigated', function() {
    const element = document.getElementById('dashboard-data');
    const occupancyLabels = JSON.parse(element.dataset.occupancyLabels);
    // ...
});
```

**Lección**: cuando se usa Livewire Navigate, evitar variables globales con `const` en scripts de página. Leer los datos del DOM en cada navegación para asegurarse de que se reciben datos frescos.

---

## Hito 7 — ECharts no renderizaba el gráfico de líneas

**Problema**: el gráfico de ocupación mostraba los ejes pero no la línea de datos. El gráfico de barras sí funcionaba correctamente.  
**Causa**: ECharts inicializa con las dimensiones del contenedor en el momento de la llamada. El tab de Alpine.js usa `x-show` que oculta el contenedor con `display: none`, haciendo que las dimensiones sean cero cuando ECharts intenta renderizar.  
**Solución**: añadir un `setTimeout` de 50ms tras inicializar para forzar un `resize()` en ambos gráficos.

```javascript
const chartOccupancy = echarts.init(occupancy);
const chartRevenue = echarts.init(revenue);

setTimeout(() => {
    chartOccupancy.resize();
    chartRevenue.resize();
}, 50);
```

**Lección**: cuando se inicializan librerías de gráficos dentro de contenedores con visibilidad condicional, siempre forzar un `resize()` tras un pequeño delay para que el navegador tenga tiempo de calcular las dimensiones reales.

---

## Hito 8 — Gráfico de ocupación mostraba todos los valores a cero

**Problema**: el gráfico de líneas del dashboard mostraba los ejes y la línea pero todos los valores eran 0, aunque existían reservas en la base de datos.  
**Causa**: el gráfico estaba configurado para mostrar las 8 semanas pasadas, pero las reservas de prueba eran de semanas futuras.  
**Solución**: cambiar el rango del bucle de `-7..0` a `-4..+3` para mostrar 4 semanas pasadas y 4 futuras, asegurando que siempre hay datos relevantes visibles.

```php
// ANTES: solo semanas pasadas
for ($i = 7; $i >= 0; $i--) { ... }

// DESPUÉS: 4 pasadas + semana actual + 3 futuras
for ($i = -4; $i <= 4; $i++) { ... }
```

**Lección**: al diseñar gráficos de ocupación o calendarios, considerar el contexto temporal de los datos de prueba. Un rango que incluya semanas futuras es más útil para un club de pádel donde las reservas se hacen con antelación.

---

## Hito 9 — Campos `disabled` no se envían en el formulario

**Problema**: al crear una clase de tipo individual, el formulario no guardaba ningún valor para `max_players` y la validación fallaba sin mostrar errores.  
**Causa**: los campos HTML con el atributo `disabled` no se incluyen en el submit del formulario. El input de plazas se deshabilitaba al seleccionar tipo "individual" pero no había ningún campo alternativo que enviara el valor.  
**Solución**: añadir un `<input type="hidden">` con el mismo nombre que siempre se envía, y sincronizarlo con el input visible mediante JavaScript:

```html
<input type="hidden" name="max_players" id="max_players_hidden" value="1">
<input type="number" id="max_players" ... disabled
       oninput="document.getElementById('max_players_hidden').value = this.value">
```

**Lección**: cuando se necesite deshabilitar un campo de formulario visualmente pero seguir enviando su valor, siempre usar un `input hidden` como respaldo. Los campos `disabled` no participan en el submit del formulario por diseño del estándar HTML.

---

## Hito 10 — Error 500 en perfil del coach (`isEmpty() on null`)

**Problema**: al acceder al perfil con un usuario de rol coach, la aplicación devolvía error 500 con el mensaje `Call to a member function isEmpty() on null`.  
**Causa**: el `ProfileController@index` inicializaba `$reservations` y `$classes` como `collect()` dentro de un bloque `if ($user->role->name === 'player')`, pero inmediatamente después las sobreescribía con consultas que buscaban reservas y clases del usuario independientemente del rol. Para el coach estas consultas devolvían resultados inesperados o nulos.  
**Solución**: reestructurar el controlador para que todas las variables se inicialicen con valores por defecto al principio, y que los bloques de consulta estén completamente separados por rol:

```php
$reservations = collect();
$classes      = collect();
$totalSpentReservations = 0;

if ($user->role->name === 'player') {
    $reservations = $user->reservations()->with('court')->get();
}

if ($user->role->name === 'coach') {
    $coachStats = [...];
}
```

**Lección**: cuando un controlador maneja múltiples roles, inicializar siempre todas las variables con valores por defecto antes de cualquier condicional. Nunca sobreescribir variables inicializadas en un bloque condicional fuera de ese bloque.

---

## Hito 11 — Inscripción duplicada en clases

**Problema**: al intentar inscribirse en una clase en la que el jugador ya había estado inscrito (y cancelado), la aplicación devolvía error 500 con `SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry`.  
**Causa**: la tabla `classes_reservations` tiene una restricción `unique(class_id, user_id)`. El controlador siempre intentaba crear un nuevo registro con `ClassRegistration::create()`, lo que viola la restricción cuando ya existe un registro cancelado para esa combinación.  
**Solución**: buscar primero si existe un registro previo y actualizar su estado en lugar de crear uno nuevo:

```php
$existing = ClassRegistration::where('class_id', $class->id)
    ->where('user_id', $user->id)
    ->first();

if ($existing) {
    if ($existing->status === 'registered') {
        return back()->with('error', 'Ya estás inscrito en esta clase.');
    }
    $existing->update(['status' => 'registered']);
} else {
    ClassRegistration::create([...]);
}
```

**Lección**: cuando una tabla tiene restricciones de unicidad y el flujo de negocio permite estados múltiples para la misma combinación de claves, es mejor buscar y actualizar el registro existente que intentar crear uno nuevo.

---

## Hito 12 — Importación de BD en Railway fallaba por warnings en el dump

**Problema**: al intentar importar el backup de la base de datos local en Railway, MySQL devolvía `ERROR 1064 (42000): You have an error in your SQL syntax`.  
**Causa**: el archivo generado por `mysqldump` incluía líneas de warning al principio del archivo (`mysqldump: [Warning] Using a password...`) que no son SQL válido. Al importar el archivo, MySQL intentaba interpretar esas líneas como sentencias SQL.  
**Solución**: limpiar el archivo eliminando las líneas que empiezan por `mysqldump:` antes de importarlo:

```bash
grep -v "^mysqldump:" backup.sql > backup_clean.sql
mysql -h caboose.proxy.rlwy.net -P 58770 -u root -pPASSWORD --skip-ssl railway < backup_clean.sql
```

**Lección**: los warnings de `mysqldump` se escriben en stdout junto con el SQL, no en stderr, por lo que contaminan el archivo de backup. Añadir `backup*.sql` al `.gitignore` para evitar subir dumps al repositorio.

---

## Hito 13 — Envío de emails en producción: Railway bloquea SMTP

**Problema**: el sistema de recuperación de contraseña funcionaba perfectamente en local pero fallaba en producción con errores de conexión o permisos.  
**Causa**: Railway bloquea todas las conexiones SMTP salientes (puertos 25, 465 y 587) en su plan gratuito. Esto impide usar cualquier servicio de email basado en SMTP directamente desde el servidor.

### Intentos fallidos en orden cronológico

**Intento 1 — Gmail SMTP (puerto 587 y 465)**  
Error: `Connection timed out` al intentar conectar con `smtp.gmail.com`.  
Causa: Railway bloquea el puerto 587. Tampoco funciona con el 465.

**Intento 2 — Resend (API HTTP)**  
Error: `The gmail.com domain is not verified`.  
Causa: Resend en su plan gratuito no permite usar dominios `@gmail.com` como remitente sin verificación de dominio. Solo permite enviar al email con el que te registraste en Resend, no a cualquier destinatario.

**Intento 3 — Brevo SMTP (puerto 587)**  
Error: `Connection timed out` al intentar conectar con `smtp-relay.brevo.com`.  
Causa: el mismo problema de Railway — bloquea SMTP independientemente del proveedor.

### Solución definitiva — Transport HTTP personalizado con Brevo API

La solución fue crear un `BrevoTransport` personalizado en `app/Mail/BrevoTransport.php` que en lugar de usar SMTP usa la API HTTP de Brevo (`https://api.brevo.com/v3/smtp/email`). Las peticiones HTTP salientes sobre el puerto 443 no están bloqueadas por Railway.

```php
// app/Mail/BrevoTransport.php
protected function doSend(SentMessage $message): void
{
    $response = Http::withHeaders([
        'api-key'      => $this->apiKey,
        'Content-Type' => 'application/json',
    ])->post('https://api.brevo.com/v3/smtp/email', $payload);

    if ($response->failed()) {
        throw new \Exception('Brevo API error: ' . $response->body());
    }
}
```

El transport se registra en `AppServiceProvider@boot`:

```php
Mail::extend('brevo', function () {
    return new BrevoTransport(config('services.brevo.key'));
});
```

Y se configura en `.env`:

```env
MAIL_MAILER=brevo
BREVO_API_KEY=tu_api_key_de_brevo
MAIL_FROM_ADDRESS=cuenta@gmail.com
MAIL_FROM_NAME="PadelSync"
```

**Lección**: Railway y otras plataformas de despliegue gratuitas frecuentemente bloquean los puertos SMTP. Para entornos de producción en estas plataformas, usar siempre servicios de email basados en API HTTP en lugar de SMTP. La diferencia clave es que SMTP necesita abrir conexiones TCP en puertos específicos, mientras que las APIs HTTP funcionan sobre el puerto 443 (HTTPS) que siempre está abierto.

---

## Hito 14 — `railway run` no ejecuta comandos en el servidor remoto

**Problema**: al intentar ejecutar `railway run php artisan migrate:fresh --seed` para sincronizar la base de datos de Railway, el comando fallaba con `could not find driver` porque no encontraba el driver de MySQL.  
**Causa**: `railway run` inyecta las variables de entorno de Railway en el proceso local y ejecuta el comando en la máquina local, no en el servidor de Railway. La máquina local no tiene el driver PDO MySQL instalado para PHP.  
**Solución**: exportar la BD local con `mysqldump`, limpiar el archivo de warnings y importarlo directamente en Railway usando el cliente MySQL con las credenciales públicas del proxy de Railway:

```bash
docker exec -it padel-db mysqldump -u user_padel -puser_pass padel_club > backup.sql
grep -v "^mysqldump:" backup.sql > backup_clean.sql
mysql -h caboose.proxy.rlwy.net -P 58770 -u root -pPASSWORD --skip-ssl railway < backup_clean.sql
```

**Lección**: `railway run` es útil para ejecutar scripts que solo necesitan las variables de entorno, pero no sustituye a una shell remota en el servidor. Para sincronizar bases de datos entre local y Railway, usar el cliente MySQL directamente con las credenciales del proxy público de Railway.

---

## Hito 15 — Scheduler en producción: Railway no tiene cron nativo

**Problema**: los comandos Artisan `classes:complete-finished` y `reservations:mark-paid` debían ejecutarse periódicamente en producción para marcar automáticamente clases y reservas como completadas/pagadas, pero Railway no ofrece cron jobs nativos en el plan gratuito.  
**Causa**: Railway no expone ninguna interfaz para programar tareas periódicas en su plan gratuito. El scheduler de Laravel (`php artisan schedule:run`) necesita ser llamado cada minuto desde el exterior.

### Solución — Endpoint protegido + cron-job.org

Se creó una ruta protegida que Railway puede recibir desde el exterior, y se usó el servicio gratuito **cron-job.org** para llamarla cada 15 minutos:

**Ruta en `routes/web.php`**:
```php
Route::get('/run-scheduler', function () {
    if (request()->header('X-Cron-Secret') !== config('app.cron_secret')) {
        abort(403);
    }
    Artisan::call('schedule:run');
    return response('OK', 200);
});
```

**Variable de entorno en Railway**:
```env
CRON_SECRET=PadelsyncTfg123
```

**Configuración en cron-job.org**:
- URL: `https://prueba1-tfg-production.up.railway.app/run-scheduler`
- Intervalo: cada 15 minutos
- Header personalizado: `X-Cron-Secret: PadelsyncTfg123`

**Comandos Artisan registrados en `routes/console.php`**:
```php
Schedule::command('classes:complete-finished')->everyFifteenMinutes();
Schedule::command('reservations:mark-paid')->everyFifteenMinutes();
```

> Un 403 al acceder al endpoint desde el navegador es el comportamiento correcto, ya que falta el header de autenticación.

**En local** el scheduler se ejecuta manualmente:
```bash
docker exec -it padel-app php artisan schedule:run
```

**Lección**: cuando la plataforma de despliegue no soporta cron jobs, la solución habitual es exponer un endpoint HTTP protegido y usar un servicio externo gratuito (cron-job.org, EasyCron, etc.) para llamarlo. El header secreto evita que cualquiera pueda disparar el scheduler desde el exterior.

---

## Hito 16 — Home autenticada con carrusel Alpine.js por rol

**Problema**: tras el login, el sistema redirigía directamente al panel específico de cada rol (dashboard admin, listado de clases del coach, reservas del jugador), sin una página de bienvenida unificada que diera contexto y acceso rápido a las funciones principales.  
**Decisión**: crear una home autenticada en `/dashboard` con un carrusel de slides adaptado al rol del usuario.

### Implementación

- **Vista**: `resources/views/dashboard.blade.php`
- **Librería**: Alpine.js (ya incluido con Livewire), sin dependencias adicionales.
- **Estructura**: carrusel con navegación por puntos y flechas, con slides diferentes según el rol.

**Slides por rol**:

| Rol | Slides |
|---|---|
| Admin | Bienvenida general · Acceso a Dashboard · Gestión de pistas · Gestión de usuarios |
| Coach | Bienvenida general · Mis clases · Crear clase |
| Player | Bienvenida general · Reservar pista · Mis clases · Mi perfil |

Cada slide incluye imagen real de pádel, título descriptivo, texto de ayuda y botón de acceso rápido a la sección correspondiente.

**Lección**: una home de bienvenida con accesos rápidos mejora la usabilidad, especialmente para roles con funcionalidades limitadas (coach, player) que de otro modo tendrían que navegar por el menú para encontrar sus opciones principales.

---

## Hito 17 — Iconos con iconify-icon (npm)

**Problema**: el proyecto usaba una combinación de Heroicons (SVG inline) y FontAwesome (CDN) según la vista, lo que generaba inconsistencia visual y dependencia de CDN externo para una parte de los iconos.  
**Decisión**: añadir **iconify-icon** como dependencia npm para unificar y ampliar el catálogo de iconos disponibles sin depender de CDN.

### Instalación

```bash
docker exec -it padel-node npm install iconify-icon
```

La librería se importa en el bundle de Vite y queda disponible como web component en todas las vistas:

```html
<iconify-icon icon="mdi:tennis" width="24"></iconify-icon>
```

**Ventajas frente a CDN**:
- Sin peticiones externas en tiempo de carga.
- Compatible con el flujo de compilación de Vite.
- Acceso a más de 200.000 iconos de múltiples colecciones (Material Design, Tabler, Phosphor, etc.).

**Lección**: instalar librerías de iconos vía npm en lugar de CDN garantiza que los iconos estén disponibles aunque el usuario tenga acceso limitado a internet, y evita bloqueos en entornos de producción restringidos.

---
 
## Hito 18 — UX de autenticación: iconos, spinner y barra de progreso Livewire
 
### Cambios realizados
 
**Iconos en formularios de autenticación**  
Se decoraron los campos de texto con iconos de la colección **Phosphor** (`ph:`) de iconify-icon, añadidos como prefijo visual dentro de los inputs y en los botones de acción.
 
**Spinner de carga personalizado (`padel-spin`)**  
Se definió la animación CSS `padel-spin` en ambos layouts (`app.blade.php` y `guest.blade.php`):
 
```css
@keyframes padel-spin {
    0%   { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.padel-spin {
    animation: padel-spin 1,2s linear infinite;
}
```
 
El spinner se muestra en los botones de submit mediante la directiva `wire:loading` de Livewire. Esto previene envíos duplicados y comunica visualmente al usuario que la acción está siendo procesada.
 
**Color de la barra de progreso de Livewire**  
Se actualizó `config/livewire.php` para alinear el color de la barra de progreso de navegación con la paleta del proyecto:
 
```php
// config/livewire.php
'progress_bar_color' => '#6b8f6b',  
```
 
**Mensajes de estado en formularios**  
Se añadieron mensajes de feedback en los formularios de auth siguiendo el sistema ya establecido en el resto de la aplicación: errores de validación inline con `@error`, mensaje de confirmación de envío de email en forgot-password y mensaje de error en el reset de contraseña.
 

**Lección**: el spinner de carga en botones de formulario es una mejora en la interfaz de la aplicación. Sin él, el usuario no percibe feedback inmediato y puede pulsar el botón varias veces, generando peticiones duplicadas. Definir la animación en el layout garantiza que esté disponible en todas las vistas sin repetir código.

---

## Hito 19 — Integración de Open-Meteo para precios dinámicos reales y bloqueo de pistas exteriores por lluvia

**Problema**: el cálculo de la tarifa nocturna usaba una tabla estática de atardeceres aproximados por mes para Sevilla, y no existía ningún mecanismo para gestionar pistas exteriores ni condiciones meteorológicas adversas.

**Decisión**: integrar la API gratuita **Open-Meteo** para obtener datos reales de amanecer, ocaso y precipitación, y añadir el concepto de pista exterior (`is_outdoor`) al modelo `Court`.

### Cambios en base de datos

**Nueva tabla `weather_cache`**:

```
date (PK)  sunrise  sunset  precipitation_mm  fetched_at
```

Almacena un registro por día con los datos meteorológicos de Open-Meteo. Actúa como caché local para que las vistas no hagan llamadas a la API en cada render.

**Nueva columna `courts.is_outdoor`** (boolean, default `false`): distingue entre pistas interiores y exteriores. Las pistas exteriores quedan bloqueadas automáticamente cuando la precipitación prevista supera el umbral de 1 mm.

### Comando Artisan `weather:fetch`

Nuevo comando en `app/Console/Commands/FetchWeatherData.php` que llama a Open-Meteo una vez al día y almacena 14 días de datos en `weather_cache`:

```php
Http::get('https://api.open-meteo.com/v1/forecast', [
    'latitude'      => 37.39,   // Sevilla
    'longitude'     => -5.99,
    'daily'         => 'sunrise,sunset,precipitation_sum',
    'timezone'      => 'Europe/Madrid',
    'forecast_days' => 14,
]);
```

Se registra en `routes/console.php` con `dailyAt('06:00')`. Los otros dos comandos (`classes:complete-finished`, `reservations:mark-paid`) mantienen su frecuencia de 15 minutos. El endpoint `/run-scheduler` existente de cron-job.org lanza los tres comandos sin cambios en la infraestructura.

### Lógica de precios y bloqueo

**`ReservationController`** y **`ClassController`** consultan `WeatherCache::forDate($date)` antes de cada operación:

- Si hay dato de ocaso real → se usa como inicio de tarifa nocturna.
- Si no hay dato en caché → se aplica el fallback estático por mes (tabla original, preservada como seguridad).
- Si `precipitation_mm >= 1.0` → las pistas con `is_outdoor = true` se excluyen de los resultados y el formulario muestra un aviso azul informativo.

La validación se aplica en dos capas: la vista filtra visualmente las pistas no disponibles, y el servidor rechaza la petición si se intenta reservar una pista exterior con lluvia prevista.

### Corrección de timezone

Se detectó que el contenedor Docker corría en UTC, lo que provocaba que las franjas horarias pasadas del día actual no se filtraran correctamente (a las 18:00 hora Madrid, `Carbon::now()` devolvía las 16:00). Solución: configurar `'timezone' => 'Europe/Madrid'` en `config/app.php`.

**Lección**: Open-Meteo es una API meteorológica gratuita, sin registro y sin API key que devuelve datos de ocaso y precipitación en una sola llamada. La combinación de una tabla de caché local + un comando diario es el patrón óptimo para este caso: cero latencia en las vistas, datos reales y sin dependencia en tiempo real de un servicio externo. El fallback estático garantiza que la aplicación funcione aunque la API esté temporalmente caída.

## Hito 20 — Plan de pruebas y tests PHPUnit

**Contexto**: el hito de integración y estabilidad del TFG requería diseñar y ejecutar un plan de pruebas con casos claros y resultados documentados.

### Estrategia adoptada

Se combinaron **tests automatizados** (PHPUnit + Livewire Volt Test) con una **lista de comprobación manual** para los flujos que no pueden cubrirse de forma automatizada (gráficos ECharts, interacciones Alpine.js, envío real de email).

La base de datos de tests usa **SQLite en memoria** (configurada en `phpunit.xml`), completamente aislada de la BD de desarrollo. Cada test usa el trait `RefreshDatabase`.

### Archivos creados

```
database/factories/UserFactory.php          ← actualizada con estados por rol
tests/Feature/AuthTest.php                  ← nuevo
tests/Feature/ReservationTest.php           ← nuevo
tests/Feature/CourtTest.php                 ← nuevo
tests/Feature/ClassTest.php                 ← nuevo
tests/Feature/SchedulerEndpointTest.php     ← nuevo
docs/TEST_PLAN.md                           ← nuevo
```

Los tests de Breeze existentes (`Auth/*`, `ProfileTest`) también se adaptaron al flujo personalizado de PadelSync.

### Resultado final

```
Tests:    51 passed (114 assertions)
Duration: 33.02s
```

### Incidencias encontradas y resueltas durante las pruebas

**I-01** — 20 tests de Breeze fallaban con `NOT NULL constraint failed: users.role_id`  
**Causa**: `UserFactory` tenía `role_id = null` por defecto.  
**Solución**: añadir `role_id` con rol `player` en el `definition()` de la factory.

**I-02** — `ClassTest` usaba status `enrolled` inexistente en el enum de BD  
**Causa**: el valor real del enum es `registered`.  
**Solución**: corregido en los asserts y en las inserciones directas de los tests.

**I-03** — `SchedulerEndpointTest` devolvía 403 con el secreto correcto  
**Causa**: `env()` en Laravel no es sobreescribible en runtime durante los tests.  
**Solución**: migrar a `config('padelsync.cron_secret')` con archivo `config/padelsync.php`. Esto también es la práctica correcta en Laravel — nunca llamar `env()` fuera de archivos de configuración.

**I-04** — `AuthTest` login/logout devolvían 405/404  
**Causa**: el auth de PadelSync usa Livewire Volt, no rutas POST clásicas.  
**Solución**: reescribir usando `Auth::attempt()` y `Auth::logout()` directamente.

**I-05** — `ProfileTest > profile page is displayed` fallaba buscando componentes Volt  
**Causa**: el perfil de PadelSync es una vista Blade estándar, no usa componentes Volt.  
**Solución**: adaptar el test a verificar el contenido HTML real de la página.

**I-06** — `RegistrationTest > new users can register` fallaba con `Attempt to read property "id" on null`  
**Causa**: el componente de registro busca el rol `player` en BD, vacía con `RefreshDatabase`.  
**Solución**: añadir `setUp()` en `RegistrationTest` que crea los 3 roles antes de cada test.

**Lección**: los tests de Breeze generados automáticamente asumen el flujo por defecto del framework. Cuando se personaliza la autenticación (campos extra en registro, redirects diferentes, perfil reconstruido), los tests de Breeze deben actualizarse para reflejar el flujo real de la aplicación. Detectar estos desajustes forma parte del valor del plan de pruebas.

---

## Hito 21 — Sistema de backup y restauración de la base de datos

**Contexto**: el hito requería implementar un sistema de copias de seguridad y restauración y comprobar que funciona.

### Decisión de implementación

Se descartó usar `mysqldump` desde el contenedor de la app porque no está garantizado en un contenedor PHP-FPM estándar (y tampoco en Railway). En su lugar, se implementaron dos comandos Artisan que usan Laravel DB directamente para generar y restaurar volcados SQL en PHP puro.

### Comandos creados

**`php artisan db:backup`** (`app/Console/Commands/BackupDatabase.php`)
- Itera todas las tablas de la BD (excepto `cache`, `sessions`, `jobs` y similares).
- Para cada tabla genera el `CREATE TABLE` (DDL real) y bloques de `INSERT` con hasta 100 filas.
- Guarda el archivo en `storage/app/backups/padelsync_backup_YYYY-MM-DD_HHmmss.sql`.
- Elimina automáticamente los backups más antiguos conservando solo los últimos 7.
- Soporta la opción `--path=` para especificar una ruta de destino diferente.

**`php artisan db:restore`** (`app/Console/Commands/RestoreDatabase.php`)
- Sin argumento: restaura el backup más reciente disponible.
- Con argumento `{file}`: restaura el archivo especificado.
- Con `--force`: omite la confirmación interactiva.
- Desactiva `FOREIGN_KEY_CHECKS` durante la restauración y los reactiva al terminar (también en caso de error).
- Parsea el SQL línea a línea sin depender de herramientas externas.

**`config/padelsync.php`** — Archivo de configuración propio del proyecto:
```php
return [
    'cron_secret' => env('CRON_SECRET'),
];
```
Centraliza la configuración específica de PadelSync. La ruta `/run-scheduler` y su test usan `config('padelsync.cron_secret')` en lugar de `env()` directamente.

### Scheduler actualizado

Se añadió el backup automático al scheduler en `routes/console.php`:

```php
// BACKUP AUTOMÁTICO DE LA BASE DE DATOS, SE EJECUTA CADA DOMINGO A LAS 03:00
app(Schedule::class)->command('db:backup')->weeklyOn(0, '03:00');
```

### Verificación en local

```
PS> docker exec -it padel-app php artisan db:backup
Iniciando backup de la base de datos...
  → Exportando tabla: classes
  → Exportando tabla: classes_reservations
  → Exportando tabla: courts
  → Exportando tabla: migrations
  → Exportando tabla: notifications
  → Exportando tabla: password_reset_tokens
  → Exportando tabla: reservations
  → Exportando tabla: roles
  → Exportando tabla: users
  → Exportando tabla: weather_cache
✓ Backup completado correctamente.
  Archivo : /var/www/storage/app/backups/padelsync_backup_2026-05-03_191746.sql
  Tamaño  : 22.4 KB

PS> docker exec -it padel-app php artisan db:restore
  ADVERTENCIA: Esta operación REEMPLAZARÁ todos los datos actuales de la BD.
  Archivo  : .../padelsync_backup_2026-05-03_191746.sql
  Tamaño   : 22.4 KB
  Base de datos destino: padel_club
 ¿Deseas continuar con la restauración? (yes/no) [no]: yes
Iniciando restauración...
✓ Restauración completada correctamente.
  Sentencias ejecutadas: 31
```

### Sistema de backup del código fuente

El **código fuente** no requiere un sistema adicional: **Git + GitHub** actúa como sistema de backup versionado. Cada `git push` guarda el estado completo del código. Lo que `db:backup` aporta es la cobertura de los **datos de la BD** (usuarios, reservas, clases), que cambian en tiempo de ejecución y no están en el repositorio.

**Lección**: implementar el backup como comandos Artisan integrados en el proyecto (en lugar de scripts externos) tiene varias ventajas: se ejecutan dentro del entorno de Laravel, tienen acceso a la configuración de la aplicación, funcionan igual en local y en Railway, y pueden programarse en el scheduler como cualquier otro comando. El backup de código fuente ya lo cubre Git — no hay que inventar nada para eso.