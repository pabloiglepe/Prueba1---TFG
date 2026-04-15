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

Ruta: `/dashboard` (accesible para todos los roles)

Al iniciar sesión, todos los usuarios acceden a una página de bienvenida común con un **carrusel de slides** adaptado a su rol. Cada slide incluye una imagen de pádel, un título descriptivo y un botón de acceso directo a la sección correspondiente.

### Slides por rol

**Administrador**
- Bienvenida general al sistema.
- Acceso directo al Dashboard de analíticas.
- Gestión de pistas.
- Gestión de usuarios.

**Entrenador**
- Bienvenida general al sistema.
- Mis clases (listado y gestión).
- Crear nueva clase.

**Jugador**
- Bienvenida general al sistema.
- Reservar una pista.
- Mis clases (inscritas y disponibles).
- Mi perfil.

El carrusel se navega mediante las flechas laterales o los puntos de posición en la parte inferior.

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