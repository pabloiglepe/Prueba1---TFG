# Manual de Uso — PadelSync

## Acceso al sistema

Al entrar en la aplicación sin sesión iniciada se muestra la **landing page** con información del club. Desde ahí se puede acceder al login o al registro.

Tras autenticarse, el sistema redirige automáticamente al panel correspondiente según el rol del usuario.

---

## Rol: Administrador

El administrador tiene acceso total a la aplicación. Su panel principal es el **Dashboard**.

### Dashboard

El dashboard se organiza en tres pestañas:

**Resumen**
- Tarjetas con KPIs: reservas totales, ingresos totales y jugadores registrados.
- Gráfico de líneas con la ocupación de pistas (4 semanas pasadas + 4 futuras). Al pulsar en un punto se abre un modal con el detalle de reservas de esa semana.
- Gráfico de barras con los ingresos de los últimos 6 meses. Al pulsar en una barra se abre un modal con el desglose por pista.

**Entrenadores**
- Listado de entrenadores con sus clases activas y número de alumnos inscritos.
- Acceso directo al perfil de cada entrenador.

**Exportar**
- Exportación de reservas en formato `.xlsx` filtradas por rango de fechas.
- Exportación de ingresos en formato `.xlsx` filtrados por mes.

### Gestión de Pistas

Ruta: `/admin/courts`

- Crear, editar y eliminar pistas.
- Cada pista tiene nombre, tipo (`cristal` / `muro`) y superficie (`césped` / `cemento`).
- Una pista con reservas futuras no puede desactivarse hasta que finalicen.
- La vista de edición muestra estadísticas de la pista: reservas totales, ingresos generados y fecha de la última reserva.

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

El proceso de creación sigue tres pasos:

1. **Seleccionar pista y fecha**: el sistema comprueba disponibilidad en tiempo real.
2. **Elegir franja horaria**: se muestran solo las franjas disponibles (sin solapamiento con otras clases ni reservas de jugadores). Duración fija de 1h 30min.
3. **Rellenar datos de la clase**: título, tipo, nivel, visibilidad, plazas máximas y precio.

### Tipos de clase

| Tipo | Visibilidad | Funcionamiento |
|---|---|---|
| Individual | `individual` | 1 plaza máxima, precio por sesión |
| Grupal pública | `public` | Los jugadores se inscriben desde su panel |
| Grupal privada | `private` | El entrenador selecciona los alumnos al crear la clase |

> Al crear una clase pública, todos los jugadores reciben una notificación automática.  
> Al inscribir alumnos en una clase privada, cada alumno recibe una notificación individual.

### Editar una clase

- Misma lógica de pasos que la creación.
- La **visibilidad no puede modificarse** una vez creada la clase.
- Se muestra el listado de alumnos inscritos con avatar e email.

### Cancelar una clase

Desde el listado de clases, el botón "Cancelar" cambia el estado de la clase a `cancelled`.

---

## Rol: Jugador

### Reservar una pista

Ruta: `/player/reservations/create`

1. Seleccionar una **fecha**.
2. El sistema muestra las **franjas horarias disponibles** (09:00 - 22:00, cada 30 minutos).
3. Al seleccionar una franja, se muestran las **pistas libres**.
4. Elegir pista y confirmar la reserva.

**Tarifa dinámica**:
- Tarifa diurna: **12 €**
- Tarifa nocturna: **16 €** (la hora de inicio varía por mes según el atardecer en Sevilla)

### Mis Reservas

Ruta: `/player/reservations`

Listado de todas las reservas con fecha, horario, pista, precio y estado. Las reservas no canceladas pueden cancelarse desde aquí.

### Clases

Ruta: `/player/classes`

Dividida en dos secciones:

- **Mis clases**: clases en las que el jugador está inscrito. Las clases futuras permiten cancelar la inscripción.
- **Clases disponibles**: clases públicas con plazas libres a las que el jugador puede inscribirse.

---

## Perfil de usuario

Ruta: `/profile` (accesible para todos los roles)

- **Datos personales**: editar nombre y teléfono. El email y el rol no son modificables.
- **Exportar datos**: descarga un JSON con todos los datos del usuario (cumplimiento RGPD).
- **Cambiar contraseña**: requiere la contraseña actual.
- **Historial** (solo jugador): reservas realizadas y clases en las que está inscrito.
- **Mis clases creadas** (solo entrenador): listado de clases con alumnos e ingresos generados.
- **Eliminar cuenta**: borrado lógico de la cuenta.

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