# Proyecto Final de Grado (DAW) - Pablo Iglesias Peral

Este repositorio contiene la aplicación de gestión para un club de pádel, desarrollada como Proyecto de Fin de Grado para el ciclo de **Desarrollo de Aplicaciones Web**. 

---

## Arquitectura del Sistema

El entorno se divide en cuatro servicios principales interconectados mediante una red interna denominada `padel-network`:

* **App (`padel-app`)**: Contenedor basado en PHP 8.2-FPM. Gestiona la lógica de backend, dependencias de Composer y la ejecución de comandos Artisan.
* **Web (`padel-web`)**: Servidor **Nginx (Alpine)** que actúa como proxy inverso, sirviendo los archivos estáticos y comunicando con el socket de PHP-FPM a través del puerto 9000.
* **DB (`padel-db`)**: Motor de base de datos **MySQL 8.0**. Utiliza persistencia de datos mapeada en el host para evitar la pérdida de información.
* **Node (`padel-node`)**: Entorno **Node.js 20** dedicado a la gestión de paquetes NPM y la compilación de assets mediante Vite.

---

## Requisitos Previos

Para levantar el proyecto en un entorno de desarrollo local, es necesario:
1.  **Docker Desktop** instalado y en ejecución.
2.  **Git** para la gestión del código fuente.

---

## Guía de Instalación y Despliegue

Sigue estos pasos en el terminal para configurar el entorno:

### 1. Clonar el repositorio
```bash
git clone https://github.com/pabloiglepe/Prueba1---TFG.git
cd ./Prueba1---TFG
```

### 2. Configuración de variables de entorno

Crea el archivo .env a partir del ejemplo proporcionado:
```bash
cp .env.example .env
```

Para que la conexión con la base de datos funcione, las variables tienen que coincidir con el servicio definido en el Compose:
<!-- * DB_HOST=padel-db
* DB_PORT=3306
* DB_DATABASE=padel_club
* DB_USERNAME=user_padel
* DB_PASSWORD=user_pass

Para acceder a la base de datos desde un cliente externo o para revisar la configuración, estas son las credenciales por defecto: 
* Base de datos: padel_club
* Usuario: user_padel
* Contraseña: user_pass
* Root Password: root -->

### 3. Construcción y arranque de contenedores
Este paso descarga las imágenes necesarias y ejecuta el Dockerfile personalizado para la aplicación
```bash
docker-compose up -d --build
```

### 4. Inicialización de la aplicación
Una vez los contenedores estén levantados, genera la clave de cifrado de Laravel:
```bash
docker-compose exec app php artisan key:generate
```