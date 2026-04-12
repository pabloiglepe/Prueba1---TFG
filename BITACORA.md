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

// FORZAMOS RESIZE TRAS INICIALIZAR POR SI EL CONTENEDOR TENÍA DIMENSIONES CERO
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
**Solución**: cambiar el rango del bucle de `-7..0` a `-4..+4` para mostrar 4 semanas pasadas y 4 futuras, asegurando que siempre hay datos relevantes visibles.

```php
// ANTES: solo semanas pasadas
for ($i = 7; $i >= 0; $i--) { ... }

// DESPUÉS: 4 pasadas + semana actual + 3 futuras
for ($i = -4; $i <= 3; $i++) { ... }
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
