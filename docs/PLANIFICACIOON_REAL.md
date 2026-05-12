# Planificación, Comparativa y Lecciones Aprendidas — PadelSync

---

## 1. Planificación final real

El desarrollo arrancó el **16 de marzo de 2026** y se cerró el **12 de mayo de 2026**:
**8 semanas efectivas** frente a las 10 semanas estimadas en la planificación preliminar.

### Cronograma real por semanas

| Semana | Fechas | Trabajo realizado | Desviación notable |
|---|---|---|---|
| **1** | 16–22 mar | Instalación del proyecto Laravel desde cero. Configuración de contenedores Docker (PHP-FPM, Nginx, MySQL). Migraciones iniciales (users, roles, courts, reservations, classes). Resolución del error 500 por permisos en `storage` y `bootstrap/cache`. | Primer commit el 16-mar. Configuración del Dockerfile de producción (Railway) ya en esta semana, no prevista hasta el final. |
| **2** | 23–29 mar | Sistema de autenticación completo: login, registro, middleware de roles, redirección post-login por rol. Fix del error de enum SQL (`pendiente` → `pending`/`paid`/`cancelled`). | Auth completada una semana antes de lo planificado. Los valores del enum en inglés se decidieron en esta fase, no en el diseño inicial. |
| **3** | 30 mar–5 abr | CRUD completo de pistas (admin) · gestión básica de usuarios · estructura base del panel del entrenador (clases individuales, grupales y privadas) · sistema de inscripción de jugadores en clases. | Fase de desarrollo backend sin commits intermedios publicados; el trabajo se consolidó localmente antes de hacer push al repositorio. |
| **4** | 6–12 abr | Notificaciones en tiempo real · perfil de usuario con tabs (Mi Perfil / Seguridad) · dashboard con tabs y exportación Excel · rediseño visual completo (landing, colores, login/registro) · **despliegue en Railway** · fix error 500 en perfil coach · fix inscripción duplicada · sistema de recuperación de contraseñas · transporte Brevo HTTP para email en producción. | El despliegue en Railway y la resolución de sus restricciones (SMTP bloqueado, errores de configuración en producción) concentraron más tiempo del previsto y retrasaron el cierre visual de la semana. |
| **5** | 13–19 abr | Scheduler de producción: endpoint `/run-scheduler` + integración cron-job.org. Home autenticada con carrusel Alpine.js por rol. Instalación de iconify-icon (npm). Corrección de timezone (`Europe/Madrid`) en el contenedor Docker. | El scheduler externo (cron-job.org) fue una solución no prevista para superar la limitación de Railway con los cron jobs. |
| **6** | 20–26 abr | Mejoras de UX en formularios de autenticación: iconos Phosphor, spinner `wire:loading`, barra de progreso verde de Livewire. Revisión del `.gitignore`. Actualizaciones de documentación. | Semana de pulido visual, sin features nuevas. |
| **7** | 27 abr–3 may | **Integración Open-Meteo**: API de tiempo real para ocaso (tarifa nocturna dinámica), precipitación (bloqueo automático de pistas exteriores), tabla `weather_cache` y comando `weather:fetch`. **Plan de pruebas**: 51 tests PHPUnit en 6 archivos + checklist manual. **Sistema de backups**: comandos `db:backup` y `db:restore` en PHP puro, sin `mysqldump`. | Open-Meteo no estaba en el plan inicial. Los tests se desarrollaron muy tarde (semana 7 de 8), lo que generó 6 incidencias que requirieron refactorizar código ya en producción. |
| **8** | 4–10 may | Nuevos KPIs del dashboard (indicadores ▲/▼ de tendencia mensual) · migración completa de todos los SVGs inline a iconify-icon/Phosphor (16 vistas) · rediseño de "Mis Reservas" con cards y tabs Alpine.js · mejoras generales de vistas. | La migración de iconos fue más costosa de lo esperado: 16 vistas revisadas manualmente. |
| **9** | 11–12 may | Nuevo gráfico de estado de reservas (donut ECharts) · reestructuración de la Home (separación `/dashboard` → redirect / `/home` → panel de actividad del jugador) · panel de gestión de backups web para admin (`/admin/backups`) · botón "Forzar backup ahora" · documentación final completa. | La UI de backups surgió de una necesidad real en producción (Railway sin acceso CLI). No estaba planificada. |

---

## 2. Comparación con la planificación preliminar

La planificación preliminar estaba estructurada en 6 hitos agrupados en 10 semanas.
A continuación se analiza cada hito comparando lo planificado con lo ejecutado.

### Tabla comparativa por hito

| Hito preliminar | Planificado | Ejecutado | Coherencia |
|---|---|---|---|
| **H1 · Infra y BD** (S1–S2) | Docker (Nginx + PHP-FPM + MySQL), migraciones base | Docker operativo en S1, migraciones en S1–S2, Dockerfile de Railway también en S1 | ✅ Cumplido con adelanto parcial |
| **H2 · Auth, Roles y Seguridad** (S3) | Login/registro con Bcrypt, middleware de roles | Completado en S2; añadidos campos RGPD (`phone_number`, `rgpd_consent`) no previstos | ✅ Cumplido; más completo que lo planificado |
| **H3 · Admin: Pistas y Dashboard** (S4–S5) | CRUD de pistas + dashboard con 3 KPIs (ocupación, ingresos, alumnos) | CRUD completo + dashboard con **3 gráficos ECharts**, Panel "Hoy", KPIs con indicadores de tendencia, exportación Excel, pestaña de entrenadores | ⚠️ Cumplido y superado ampliamente. El dashboard resultó mucho más complejo de lo estimado |
| **H4 · Academia y Reservas** (S6–S7) | Disponibilidad del entrenador + motor de reservas con anti-solapamiento | Motor de reservas completo ✅. Clases individual/grupal pública/privada ✅. **La gestión de disponibilidad como módulo independiente no se implementó**: los horarios se gestionan directamente al crear la clase | ⚠️ Parcialmente cumplido; el requisito de "definir disponibilidad" quedó integrado de forma implícita |
| **H5 · Frontend y RGPD** (S8–S9) | Vistas Blade/Alpine.js + consentimiento + borrado lógico + exportación de datos | RGPD completo (consent, soft delete, JSON export) ✅. Añadidos: home con carrusel, rediseño de Mis Reservas con cards, migración completa a iconify-icon, UX de auth | ✅ Cumplido; el frontend se desarrolló en paralelo con el backend, no al final |
| **H6 · Pruebas y Despliegue** (S10) | PHPUnit unitarios + **Cypress/Playwright E2E** + manual de usuario + despliegue documentado | PHPUnit 51 tests ✅; **Cypress/Playwright: no implementados** ❌; checklist de pruebas manuales ✅; despliegue en Railway ✅; documentación técnica completa ✅ | ⚠️ Las pruebas E2E no se ejecutaron; compensadas con mayor cobertura manual documentada |

### Análisis de coherencia y causas de las desviaciones

**Grado de coherencia global: alto en el qué, bajo en el cómo y el cuándo.**

El 90 % de los requisitos funcionales definidos en la Entrega 2 se entregaron.
Las causas de las desviaciones se agrupan en cuatro categorías:

**1. Subestimación del coste de la interfaz y la UX**

La planificación preliminar dedicaba las semanas 8–9 exclusivamente al frontend, asumiendo
que era una capa que se añadiría al final. En la práctica, cada funcionalidad de backend
requería su vista correspondiente de forma inmediata para poder probarse, lo que hizo que
frontend y backend se desarrollaran siempre en paralelo. Esto explica el sprint mayor de la
semana 4, donde se entregaron simultáneamente backend y frontend de casi todas las
funcionalidades del sistema.

**2. Restricciones no previstas de la plataforma de producción (Railway)**

La planificación no contemplaba las limitaciones específicas de Railway: bloqueo de puertos
SMTP, filesystem efímero, ausencia de cron nativo, imposibilidad de ejecutar comandos
remotos con `railway run`. Cada restricción requirió una solución alternativa no planificada:

- Brevo HTTP transport → sustituyó al SMTP estándar
- cron-job.org + endpoint protegido → sustituyó al cron nativo
- `db:backup` en PHP puro → sustituyó a `mysqldump`
- Panel web de backups → sustituyó al acceso por CLI

**3. Scope creep controlado**

La integración de Open-Meteo (tarifa nocturna dinámica, bloqueo de pistas exteriores por
lluvia) y el panel web de backups no estaban en el plan inicial, pero fueron implementados como
respuesta a necesidades reales detectadas durante el desarrollo. Ambas funcionalidades
añaden valor diferencial pero supusieron tiempo no planificado.

**4. Las pruebas E2E no se ejecutaron**

El plan incluía Cypress o Playwright para pruebas de extremo a extremo. En la práctica, la
suite de PHPUnit con 51 tests cubrió los flujos críticos y el checklist de pruebas manuales
cubrió los casos visuales y JavaScript. Cypress no se instaló por no justificar el coste de
configuración para el alcance de un proyecto individual.

---

## 3. Lecciones aprendidas

### 3.1 Qué se hizo bien y repetiría

**Bitácora técnica desde el primer día.**
Documentar cada problema con su causa, solución y lección aprendida en el momento en que
ocurre es la práctica de mayor valor de todo el proyecto. Al llegar a la semana 7, fue posible
reconstruir exactamente qué decisiones se tomaron y por qué sin depender de la memoria.

**Iteraciones cortas backend + frontend.**
Desarrollar cada funcionalidad de principio a fin (modelo → controlador → vista) en la misma
sesión evitó el riesgo de encontrar incompatibilidades entre capas al final. El sprint de la
semana 4 demostró que este enfoque funciona incluso en periodos de alta densidad.

**Caché local para APIs externas.**
El patrón `weather_cache` (tabla de un registro por día, rellenada por un comando programado,
consultada por los controladores sin llamadas en tiempo real) es el correcto para cualquier
API de terceros: cero latencia en las vistas, datos actualizados una vez al día y fallback
automático si la API falla. Se aplicaría igual en una versión 2.

**Git como única fuente de verdad del código.**
Nunca se perdió trabajo. Cada `push` fue el backup del código. La lección del Hito 12
(warnings de `mysqldump` en stdout) reforzó por qué los backups del código y los de datos
son problemas distintos que no deben mezclarse.

**Separación de `env()` y `config()` desde el inicio.**
Migrar `env('CRON_SECRET')` a `config('padelsync.cron_secret')` fue un cambio pequeño que
resolvió un problema de tests y además es la práctica correcta en Laravel. Se aplicaría esta
separación desde el primer archivo de configuración en cualquier proyecto siguiente.

---

### 3.2 Errores que no volvería a cometer

**No hacer ninguna planificación al inicio.**
El proyecto arrancó directamente con código sin definir estructura robusta, prioridades ni estimaciones.
Eso derivó en la semana 3 sin commits, en el scope creep de Open-Meteo y el panel de
backups, y en las pruebas E2E que quedaron sin implementar. Una planificación más profesional y detallada habría detectado las restricciones de Railway antes del despliegue y habría distribuido el trabajo de forma más uniforme.

**Dejar los tests para el final.**
En la semana 7 los tests revelaron 6 incidencias que requirieron modificar código de
producción ya desplegado (`UserFactory`, enum de estados, configuración del endpoint del
scheduler). Si los tests se hubieran escrito en paralelo con el código, estas incidencias se
habrían detectado y resuelto al momento, con un coste mucho menor. Escribir el test en el
mismo momento en que se escribe el controlador es la única forma de que los tests sean
útiles preventivamente.

**Mezclar tres fuentes de iconos sin establecer una convención desde el inicio.**
La semana 8 se dedicó en gran parte a la migración completa de iconos (16 vistas), un trabajo
puramente correctivo que no aportó ninguna funcionalidad nueva. Si desde la primera vista se
hubiera establecido la convención de usar iconify-icon/Phosphor, esa semana entera habría
estado disponible para otras tareas.

**No usar una rama `develop` a pesar de tenerlo planificado.**
Todos los commits fueron directamente a `master`, incluyendo fixes urgentes de producción
mezclados con desarrollo de nuevas funcionalidades. En el próximo proyecto: `master` solo
para versiones estables; `develop` para el trabajo diario.

---

### 3.3 Tres mejoras para una versión 2

**Mejora 1 — Introducir una capa de servicios (Service Layer)**

En la versión actual, los controladores contienen lógica de negocio compleja, ej: el
`ReservationController` valida la disponibilidad horaria, consulta el tiempo, calcula la tarifa
y guarda la reserva en el mismo método. En una v2, toda esa lógica se extraería a clases de
servicio (`ReservationService`, `PricingService`, `WeatherService`). Los controladores
quedarían con una sola responsabilidad: recibir la petición, delegar en el servicio y devolver
la respuesta. Esto haría los tests unitarios triviales, el código más legible y el mantenimiento
más sencillo.

**Mejora 2 — Pagos reales con Stripe o similar**

La tarifa de reservas se calcula (12 € / 16 €) y se registra en base de datos, pero el pago es
completamente manual: el admin o el scheduler marcan la reserva como `paid` sin que haya
ninguna transacción real. En una v2, integrar Stripe Checkout añadiría el flujo completo: el
jugador paga al reservar, se genera un recibo, el admin ve los ingresos reales verificados.
Esto convertiría PadelSync en un sistema operativo real y no solo de gestión.

**Mejora 3 — Persistencia de backups en almacenamiento externo**

El sistema actual de backups es local al contenedor de Railway, que es efímero: si el
contenedor se reinicia, los backups se pierden. En una v2 se integraría el almacenamiento de
backups en un servicio externo (Cloudflare, AWS o similar): el comando
`db:backup` generaría el `.sql` y lo subiría al bucket; el `BackupController` listaría y
restauraría desde ahí. Esto resolvería definitivamente la limitación documentada en el Hito 25
y haría el sistema de backups fiable en producción.