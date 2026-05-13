# Manual de Uso — PadelSync

## Acceso al sistema

Al entrar en la aplicación sin sesión iniciada se muestra la **landing page** con información del club. Desde ahí se puede acceder al login o al registro.

Tras autenticarse, el sistema redirige automáticamente a la **home autenticada** (`/dashboard`), donde se presenta un carrusel de bienvenida con accesos rápidos adaptados al rol del usuario.

### Recuperación de contraseña

Si el usuario no recuerda su contraseña puede recuperarla desde la pantalla de login pulsando **¿Olvidaste la contraseña?**. El flujo es el siguiente:

1. Introduce el email asociado a la cuenta.
2. El sistema envía un email con un enlace de recuperación (válido durante 60 minutos).
3. Al pulsar el enlace, se accede a un formulario para establecer una nueva contraseña.
4. Tras confirmar la nueva contraseña, el sistema redirige al login.

---

## Home autenticada

Ruta: `/home` (accesible para todos los roles desde el enlace "Inicio" de la barra de navegación)

### Redirección tras login

Tras autenticarse, el sistema redirige a todos los roles directamente a la **home autenticada** (`/home`) con el carrusel de bienvenida. Desde ahí cada usuario accede a su sección principal mediante el menú de navegación o los accesos rápidos del carrusel.

### Carrusel de slides

La home muestra un **carrusel de slides** adaptado al rol del usuario, con avance automático cada 4 segundos. Cada slide incluye una imagen de pádel, un título descriptivo y un botón de acceso directo.

**Administrador**: Bienvenida · Dashboard · Gestión de pistas · Gestión de usuarios  
**Entrenador**: Bienvenida · Mis clases · Estadísticas del perfil  
**Jugador**: Bienvenida · Reservar pista · Mejorar nivel con clases · Tu club en tu mano

El carrusel se navega también con las flechas laterales o los puntos de posición.

### Panel de actividad (solo jugador)

Debajo del carrusel, los jugadores ven un panel personalizado con sus datos en tiempo real:

- **Próximas reservas**: listado de las próximas reservas activas con pista, fecha/hora, precio y estado. Muestra el total de reservas del mes.
- **Próximas clases**: listado de las próximas clases inscritas con título, fecha y entrenador. Muestra el gasto del mes.
- **Accesos rápidos**: tarjetas de acceso directo a Reservar pista, Clases disponibles y Mi perfil.

---

## Rol: Administrador

El administrador tiene acceso total a la aplicación. Su panel principal es el **Dashboard**.

### Dashboard

El dashboard se organiza en tres pestañas:

**Resumen**
- **Panel "Hoy"**: estado en tiempo real de todas las pistas activas (Libre / Ocupada hasta HH:MM por nombre del jugador), número de reservas del día e ingresos generados hasta el momento.
- **Tarjetas KPI**: reservas totales, ingresos totales y jugadores registrados. Cada tarjeta de reservas e ingresos incluye un indicador de tendencia (▲/▼ porcentaje) comparando el mes actual con el mes anterior.
- **Gráfico de estados** (circular/donut): distribución global de reservas por estado — Completadas, Pendientes y Canceladas.
- **Gráfico de ocupación** (líneas): reservas por semana (4 semanas pasadas + 4 futuras). Al pulsar en un punto se abre un modal con el listado de reservas de esa semana.
- **Gráfico de ingresos** (barras): ingresos de los últimos 6 meses. Al pulsar en una barra se abre un modal con el desglose por pista y el listado de reservas del mes.

**Entrenadores**
- Listado de entrenadores con sus clases activas, número de alumnos inscritos, nivel y visibilidad de cada clase.
- Acceso directo al perfil completo de cada entrenador.

**Exportar Informes**
- Exportación de reservas en formato `.xlsx` filtradas por rango de fechas.
- Exportación de ingresos en formato `.xlsx` filtrados por mes.

### Gestión de Pistas

Ruta: `/admin/courts`

- Crear, editar y eliminar pistas.
- Cada pista tiene nombre, tipo (`cristal` / `muro`), superficie (`césped` / `cemento`) y ubicación (`interior` / `exterior`).
- Las pistas exteriores (`is_outdoor`) se bloquean automáticamente para reservas y clases cuando la previsión de lluvia supera 1 mm.
- Una pista con reservas futuras no puede desactivarse hasta que finalicen.
- La vista de edición muestra estadísticas de la pista: reservas totales, ingresos generados y fecha de la última reserva.

### Ajustes del Club

Ruta: `/admin/settings`

Sección exclusiva del administrador para configurar los parámetros operativos del club. Todos los valores se almacenan en la tabla `club_settings` y se aplican en tiempo real sin necesidad de modificar código.

| Campo | Descripción | Valores permitidos |
|---|---|---|
| Hora de apertura | Inicio de la jornada (primer slot disponible) | Formato HH:MM |
| Hora de cierre | Fin de la jornada | Formato HH:MM (debe ser posterior a apertura) |
| Intervalo de franjas | Separación entre slots horarios | 15, 30 o 60 minutos |
| Duración de la reserva/clase | Duración fija de cada reserva y clase | 60, 90 o 120 minutos |
| Tarifa diurna | Precio de reserva en horario diurno | Valor numérico (€) |
| Tarifa nocturna | Precio de reserva en horario nocturno | Valor numérico (€) |

> Los cambios afectan inmediatamente a la generación de franjas horarias en la creación de reservas y clases, a la validación de solapamientos y a los precios mostrados al jugador.

---

### Gestión de Backups

Ruta: `/admin/backups`

Sección exclusiva del administrador para gestionar las copias de seguridad de la base de datos generadas automáticamente por el scheduler cada noche a las 03:00.

- Listado de archivos `.sql` disponibles en `storage/app/backups/`, ordenados del más reciente al más antiguo.
- Cada card muestra el nombre del archivo, la fecha y hora de generación y el tamaño del archivo. El backup más reciente aparece marcado con un badge "Más reciente".
- Botón **"Forzar backup ahora"** en la cabecera: genera un backup nuevo al instante sin necesidad de esperar al scheduler ni acceder por CLI. Útil para forzar un backup antes de una restauración o un cambio importante.
- Botón **Restaurar** en cada card: reemplaza todos los datos actuales de la base de datos con los del backup seleccionado. El sistema pide confirmación antes de proceder.
- Si no hay ningún backup disponible, la vista muestra un mensaje informativo indicando que el backup automático se ejecuta diariamente a las 03:00.

> Esta funcionalidad es útil principalmente en entorno local (Docker). En Railway el filesystem del contenedor es efímero, por lo que los backups pueden perderse si el contenedor se reinicia.

### Gestión de Usuarios

Ruta: `/admin/users`

- Listado de jugadores y entrenadores con buscador integrado.
- Crear nuevos usuarios asignándoles rol.
- Editar datos de usuario. La vista de edición muestra estadísticas diferenciadas:
  - **Jugador**: reservas totales, gasto total, fecha de registro y estado RGPD.
  - **Entrenador**: clases creadas, ingresos generados, fecha de registro y estado RGPD.

---

## Rol: Entrenador

El entrenador gestiona sus propias clases desde `/coach/classes`.

### Crear una clase

El proceso de creación sigue cuatro pasos:

1. **Seleccionar fecha**: si hay lluvia prevista, se muestra un aviso y solo se muestran pistas cubiertas.
2. **Seleccionar pista** disponible para esa fecha.
3. **Elegir franja horaria**: se muestran solo las franjas disponibles (sin solapamiento con otras clases ni reservas de jugadores). La duración, el intervalo entre franjas y el horario de operación se obtienen de los ajustes configurados por el administrador.
4. **Rellenar datos de la clase**: título, tipo, nivel, visibilidad, plazas máximas y precio.

### Tipos de clase

| Tipo | Visibilidad | Funcionamiento |
|---|---|---|
| Individual | `individual` | 1 plaza máxima forzada, precio por sesión |
| Grupal pública | `public` | Los jugadores se inscriben desde su panel |
| Grupal privada | `private` | El entrenador selecciona los alumnos al crear o editar la clase |

> Al crear una clase pública, todos los jugadores reciben una notificación automática.  
> Al inscribir alumnos en una clase privada (en creación o edición), cada alumno recibe una notificación individual.

### Restricciones de inscripción

- **Clase individual**: solo puede tener **1 alumno**. Al seleccionar el tipo "Individual" los checkboxes se comportan como selección única y el campo Plazas máximas se fija automáticamente a 1.
- **Límite de plazas**: no se pueden marcar más alumnos que el valor indicado en "Plazas máximas". Los checkboxes sobrantes se deshabilitan automáticamente al alcanzar el límite.
- Ambas restricciones se validan también en el servidor como segunda capa de seguridad.

### Editar una clase

- Misma lógica de pasos que la creación (fecha, pista, franja, datos).
- La **visibilidad no puede modificarse** una vez creada la clase.
- **Clases privadas**: panel de checkboxes editable con los alumnos actualmente inscritos pre-marcados. Marcar un alumno nuevo lo inscribe y le envía una notificación; desmarcar un alumno inscrito cancela su inscripción.
- **Clases públicas**: lista informativa de inscritos en modo lectura (los jugadores se autoinscriben desde su panel).
- Se aplican las mismas restricciones de límite de plazas e individual que en la creación.

### Mis Clases — tabs

El listado de clases se organiza en tres pestañas:

- **Programadas**: clases con estado `Programada`. Permiten editar y cancelar.
- **Completadas**: clases que el scheduler ha marcado como finalizadas. Solo lectura.
- **Canceladas**: clases canceladas, con paleta visual en rojo apagado. Solo lectura.

### Cancelar una clase

Desde el listado, el botón "Cancelar" de la pestaña Programadas cambia el estado de la clase a `cancelled`.

---

## Rol: Jugador

### Reservar una pista

Ruta: `/player/reservations/create`

1. Seleccionar una **fecha**.
2. Si hay lluvia prevista, aparece un aviso informativo y las pistas exteriores no están disponibles.
3. El sistema muestra las **franjas horarias disponibles** según el horario de apertura/cierre e intervalo configurados por el administrador. Solo se muestran franjas con al menos una pista libre (sin reservas ni clases activas en ese tramo).
4. Al seleccionar una franja, se muestran las **pistas libres** (interiores siempre; exteriores solo si no llueve, y excluidas las que tengan una clase programada en ese horario).
5. Elegir pista y confirmar la reserva.

**Tarifa dinámica**:
- Tarifa diurna y nocturna configurables desde el panel de administración (`/admin/settings`).
- La hora de transición diurna/nocturna se calcula con la hora de ocaso real obtenida de Open-Meteo; fallback estático por mes si no hay dato disponible.

### Mis Reservas

Ruta: `/player/reservations`

Vista organizada en tres pestañas (la activa por defecto es **Pendientes**):

- **Pendientes**: reservas con estado `pending`. Tarjeta blanca con badge ámbar. Permite cancelar la reserva.
- **Pagadas**: reservas con estado `paid`. Tarjeta en verde claro con badge verde. Solo lectura.
- **Canceladas**: reservas canceladas. Tarjeta en rojo apagado con badge rojo. Solo lectura.

### Clases

Ruta: `/player/classes`

Dividida en dos secciones:

- **Mis clases**: clases en las que el jugador está inscrito. Las clases futuras permiten cancelar la inscripción. Si el jugador cancela y quiere volver a inscribirse, el sistema permite la reinscripción sin errores.
- **Clases disponibles**: clases públicas con plazas libres a las que el jugador puede inscribirse.

---

## Perfil de usuario

Ruta: `/profile` (accesible para todos los roles)

El perfil se organiza en dos pestañas:

### Mi Perfil

- **Tarjetas resumen** diferenciadas por rol:
  - Jugador: gasto en reservas, gasto en clases y gasto total.
  - Entrenador: clases creadas, alumnos totales e ingresos generados.
- **Datos personales**: editar nombre y teléfono. El email y el rol no son modificables.
- **Exportar datos**: descarga un JSON con todos los datos del usuario (cumplimiento RGPD).
- **Historial de reservas** (solo jugador): listado con fecha, pista, horario, precio y estado.
- **Mis clases** (solo jugador): clases en las que está inscrito con entrenador, pista y precio.
- **Mis clases creadas** (solo entrenador): listado con alumnos inscritos, estado e ingresos generados.

### Seguridad

- **Cambiar contraseña**: requiere introducir la contraseña actual antes de establecer la nueva.
- **Zona de peligro**: eliminar la cuenta de forma permanente. Requiere confirmar la contraseña. El borrado es lógico (softDelete) y cancela todas las reservas pendientes.

---

## Sistema de notificaciones

La campana en la barra de navegación muestra el número de notificaciones no leídas. Al pulsar se despliega un listado con:

- Título y mensaje de la notificación.
- Tiempo transcurrido desde la notificación.
- Botón para marcar como leída individualmente.
- Botón para marcar todas como leídas.

Los eventos que generan notificaciones son:
- Inscripción a una clase privada por parte del entrenador.
- Creación de una nueva clase pública.