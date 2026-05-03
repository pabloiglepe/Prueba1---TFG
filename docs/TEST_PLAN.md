# Plan de Pruebas — PadelSync

> Última ejecución: **51 tests pasados, 0 fallos, 114 assertions** — `php artisan test`

---

## 1. Estrategia de pruebas

El plan de pruebas de PadelSync combina **tests automatizados** (PHPUnit + Livewire Volt Test) con una **lista de comprobación manual** para los flujos que no pueden cubrirse de forma automatizada (renderizado de gráficos ECharts, interacciones Alpine.js, envío real de email).

| Tipo | Herramienta | Cobertura |
|---|---|---|
| Feature tests (HTTP + roles) | PHPUnit / Laravel TestCase | Control de acceso, rutas, lógica de negocio |
| Component tests (Livewire) | `Volt::test()` | Formularios de auth y perfil |
| Pruebas manuales | Checklist documentado | UI, gráficos, email, scheduler |

La base de datos de tests es **SQLite en memoria** (configurada en `phpunit.xml`), aislada de la BD de desarrollo. Cada test usa el trait `RefreshDatabase` para partir de un estado limpio.

---

## 2. Archivos de test

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
    ├── AuthTest.php
    ├── ClassTest.php
    ├── CourtTest.php
    ├── ExampleTest.php
    ├── ProfileTest.php
    ├── ReservationTest.php
    └── SchedulerEndpointTest.php
```

---

## 3. Casos de prueba automatizados

### 3.1 Autenticación y sesión (`AuthTest`)

| ID | Caso | Resultado esperado | Estado |
|---|---|---|---|
| A-01 | Login con credenciales válidas | `Auth::attempt()` devuelve `true`, usuario autenticado | ✅ PASS |
| A-02 | Login con contraseña incorrecta | `Auth::attempt()` devuelve `false`, usuario no autenticado | ✅ PASS |
| A-03 | Acceso a ruta protegida sin autenticar | Redirect a `/login` | ✅ PASS |
| A-04 | Logout de usuario autenticado | Usuario desautenticado tras `Auth::logout()` | ✅ PASS |

### 3.2 Control de acceso por rol (`AuthTest`)

| ID | Caso | Resultado esperado | Estado |
|---|---|---|---|
| R-01 | Player intenta acceder a `/admin/dashboard` | HTTP 403 Forbidden | ✅ PASS |
| R-02 | Coach intenta acceder a `/admin/dashboard` | HTTP 403 Forbidden | ✅ PASS |
| R-03 | Admin accede a `/player/reservations` | HTTP 200 OK | ✅ PASS |
| R-04 | Admin accede a `/coach/classes` | HTTP 200 OK | ✅ PASS |

### 3.3 Flujo de autenticación Livewire (`Auth/AuthenticationTest`)

| ID | Caso | Resultado esperado | Estado |
|---|---|---|---|
| L-01 | Pantalla de login se renderiza | HTTP 200, componente Volt `pages.auth.login` visible | ✅ PASS |
| L-02 | Login correcto vía componente Volt | Sin errores, redirect a `/home` | ✅ PASS |
| L-03 | Login incorrecto vía componente Volt | Con errores, sin redirect, usuario no autenticado | ✅ PASS |
| L-04 | Navbar se renderiza en `/home` | HTTP 200, componente `layout.navigation` visible | ✅ PASS |
| L-05 | Logout vía componente Volt | Sin errores, redirect a `/`, usuario desautenticado | ✅ PASS |

### 3.4 Registro de usuarios (`Auth/RegistrationTest`)

| ID | Caso | Resultado esperado | Estado |
|---|---|---|---|
| RE-01 | Pantalla de registro se renderiza | HTTP 200, componente Volt `pages.auth.register` visible | ✅ PASS |
| RE-02 | Registro completo con todos los campos | Redirect a `/dashboard`, usuario autenticado | ✅ PASS |

> **Nota**: El registro de PadelSync requiere `phone_number` y `rgpd_consent` además de los campos base. El test inicializa los roles (`admin`, `coach`, `player`) en `setUp()` porque el componente de registro necesita el rol `player` en la BD.

### 3.5 Verificación y cambio de contraseña (`Auth/*`)

| ID | Caso | Resultado esperado | Estado |
|---|---|---|---|
| V-01 | Pantalla de verificación de email se renderiza | HTTP 200 | ✅ PASS |
| V-02 | Email verificado con hash válido | Usuario verificado | ✅ PASS |
| V-03 | Hash inválido no verifica el email | Email no verificado | ✅ PASS |
| V-04 | Pantalla de confirmación de contraseña | HTTP 200 | ✅ PASS |
| V-05 | Contraseña correcta confirmada | Sin errores | ✅ PASS |
| V-06 | Contraseña incorrecta no confirmada | Con errores | ✅ PASS |
| V-07 | Pantalla de reset de contraseña | HTTP 200 | ✅ PASS |
| V-08 | Solicitud de enlace de reset | Email enviado (fake) | ✅ PASS |
| V-09 | Reset con token válido | Contraseña actualizada | ✅ PASS |
| V-10 | Cambio de contraseña con contraseña actual correcta | Sin errores | ✅ PASS |
| V-11 | Cambio de contraseña con contraseña actual incorrecta | Con errores | ✅ PASS |

### 3.6 Gestión de pistas (`CourtTest`)

| ID | Caso | Resultado esperado | Estado |
|---|---|---|---|
| P-01 | Admin puede ver el listado de pistas | HTTP 200 | ✅ PASS |
| P-02 | Player no puede acceder a gestión de pistas | HTTP 403 | ✅ PASS |
| P-03 | Coach no puede acceder a gestión de pistas | HTTP 403 | ✅ PASS |
| P-04 | Admin puede crear una pista nueva | HTTP redirect, pista guardada en BD | ✅ PASS |
| P-05 | No se puede desactivar pista con reservas futuras | Pista permanece activa en BD | ✅ PASS |

### 3.7 Reservas de pistas (`ReservationTest`)

| ID | Caso | Resultado esperado | Estado |
|---|---|---|---|
| RES-01 | Player puede ver la página de reservas | HTTP 200 | ✅ PASS |
| RES-02 | Coach no puede acceder a reservas de player | HTTP 403 | ✅ PASS |
| RES-03 | Player puede cancelar su propia reserva | Status cambia a `cancelled` en BD | ✅ PASS |
| RES-04 | Player no puede cancelar reserva ajena | Reserva ajena permanece sin cambios | ✅ PASS |

### 3.8 Clases de pádel (`ClassTest`)

| ID | Caso | Resultado esperado | Estado |
|---|---|---|---|
| C-01 | Coach puede ver su listado de clases | HTTP 200 | ✅ PASS |
| C-02 | Player no puede acceder a crear clases | HTTP 403 | ✅ PASS |
| C-03 | Player puede ver las clases disponibles | HTTP 200 | ✅ PASS |
| C-04 | Player puede inscribirse en clase pública | Registro con status `registered` en BD | ✅ PASS |
| C-05 | Player no puede inscribirse dos veces en la misma clase | Solo existe 1 registro en BD | ✅ PASS |

### 3.9 Perfil de usuario (`ProfileTest`)

| ID | Caso | Resultado esperado | Estado |
|---|---|---|---|
| PR-01 | Página de perfil se renderiza correctamente | HTTP 200, contiene "Mi Perfil", "Datos personales", "Seguridad" | ✅ PASS |
| PR-02 | Datos de perfil pueden actualizarse | Nombre y email actualizados en BD | ✅ PASS |
| PR-03 | Email sin cambios no resetea la verificación | `email_verified_at` permanece | ✅ PASS |
| PR-04 | Cuenta puede eliminarse con contraseña correcta | Usuario eliminado, sesión cerrada | ✅ PASS |
| PR-05 | Cuenta no se elimina con contraseña incorrecta | Error de validación, usuario permanece | ✅ PASS |

### 3.10 Endpoint del scheduler (`SchedulerEndpointTest`)

| ID | Caso | Resultado esperado | Estado |
|---|---|---|---|
| SC-01 | `/run-scheduler` sin header `X-Cron-Secret` | HTTP 403 | ✅ PASS |
| SC-02 | `/run-scheduler` con secreto incorrecto | HTTP 403 | ✅ PASS |
| SC-03 | `/run-scheduler` con secreto correcto | HTTP 200, respuesta `OK` | ✅ PASS |

> **Nota técnica**: El endpoint usa `config('padelsync.cron_secret')` en lugar de `env('CRON_SECRET')` directamente. Esto permite sobreescribir el valor en tests con `config([...])`, ya que `env()` no es sobreescribible en runtime en Laravel.

---

## 4. Resultado de la ejecución automatizada

```
Tests:    51 passed (114 assertions)
Duration: 33.02s
```

Comando de ejecución:
```bash
docker exec -it padel-app php artisan test
```

Para ejecutar un grupo específico:
```bash
docker exec -it padel-app php artisan test tests/Feature/AuthTest.php
docker exec -it padel-app php artisan test tests/Feature/CourtTest.php
docker exec -it padel-app php artisan test tests/Feature/ReservationTest.php
docker exec -it padel-app php artisan test tests/Feature/ClassTest.php
docker exec -it padel-app php artisan test tests/Feature/SchedulerEndpointTest.php
```

---

## 5. Pruebas manuales

Los siguientes flujos no son automatizables por depender de renderizado visual, interacciones JavaScript o servicios externos.

### 5.1 Dashboard admin — Gráficos ECharts

| ID | Acción | Resultado esperado | Estado |
|---|---|---|---|
| M-01 | Acceder a `/admin/dashboard` | Gráfico de líneas (ocupación semanal) y gráfico de barras (ingresos mensuales) se renderizan sin errores | ✅ OK |
| M-02 | Clic en semana del gráfico de líneas | Aparece listado de reservas de esa semana | ✅ OK |
| M-03 | Clic en mes del gráfico de barras | Aparece desglose por pista y listado de reservas | ✅ OK |

### 5.2 Home autenticada — Carrusel Alpine.js

| ID | Acción | Resultado esperado | Estado |
|---|---|---|---|
| M-04 | Login como `admin` | Home muestra carrusel con slides e iconos de acceso rápido de admin | ✅ OK |
| M-05 | Login como `coach` | Home muestra slides adaptados al rol de entrenador | ✅ OK |
| M-06 | Login como `player` | Home muestra slides adaptados al rol de jugador | ✅ OK |
| M-07 | Clic en flechas de navegación del carrusel | Slides avanzan/retroceden correctamente | ✅ OK |

### 5.3 Sistema de email — Forgot password

| ID | Acción | Resultado esperado | Estado |
|---|---|---|---|
| M-08 | Solicitar reset de contraseña en local | Email enviado vía SMTP Brevo, recibido en bandeja | ✅ OK |
| M-09 | Solicitar reset de contraseña en Railway | Email enviado vía HTTP API Brevo (puerto 443), recibido en bandeja | ✅ OK |
| M-10 | Usar enlace de reset con token expirado | Pantalla de error, no permite el cambio | ✅ OK |

### 5.4 Scheduler — cron-job.org

| ID | Acción | Resultado esperado | Estado |
|---|---|---|---|
| M-11 | cron-job.org llama a `/run-scheduler` cada 15 min | Railway responde 200, log de Railway muestra ejecución | ✅ OK |
| M-12 | Reserva cuya hora ha pasado | Scheduler la marca como `paid` automáticamente | ✅ OK |
| M-13 | Clase cuya hora ha pasado | Scheduler la marca como `completed` automáticamente | ✅ OK |

### 5.5 Navegación y roles en producción (Railway)

| ID | Acción | Resultado esperado | Estado |
|---|---|---|---|
| M-14 | Login como admin en Railway | Acceso a panel admin, dashboard con gráficos | ✅ OK |
| M-15 | Login como coach en Railway | Acceso a gestión de clases, sin panel admin | ✅ OK |
| M-16 | Login como player en Railway | Acceso a reservas y clases, sin panel admin | ✅ OK |
| M-17 | Intentar acceder a `/admin` como player | Pantalla de error 403 | ✅ OK |

---

## 6. Incidencias detectadas durante las pruebas

| ID | Descripción | Causa | Solución aplicada |
|---|---|---|---|
| I-01 | 20 tests de Breeze fallaban con `NOT NULL constraint failed: users.role_id` | `UserFactory` tenía `role_id = null` por defecto | Se añadió `role_id` con rol `player` en el `definition()` de la factory |
| I-02 | `ClassTest` usaba status `enrolled` inexistente en el enum de BD | El valor real del enum es `registered` | Corregido en los tests y en la inserción directa |
| I-03 | `SchedulerEndpointTest` devolvía 403 con secreto correcto | `env()` no es sobreescribible en runtime de Laravel | Se migró a `config('padelsync.cron_secret')` con archivo `config/padelsync.php` |
| I-04 | `AuthTest` login/logout devolvían 405/404 | El auth de PadelSync usa Livewire Volt, no rutas POST clásicas | Se reescribieron usando `Auth::attempt()` y `Auth::logout()` directamente |
| I-05 | `ProfileTest > profile page is displayed` fallaba buscando componentes Volt | El perfil de PadelSync es una vista Blade estándar, no usa Volt | Se adaptó el test a verificar el contenido HTML real de la página |
| I-06 | `RegistrationTest > new users can register` fallaba con `Attempt to read property "id" on null` | El componente de registro busca el rol `player` en BD, vacía con `RefreshDatabase` | Se añadió `setUp()` en `RegistrationTest` que crea los 3 roles antes de cada test |