# Proyecto Final de Grado (DAW) - Pablo Iglesias Peral

Este repositorio contiene la aplicación de gestión para un club de pádel, desarrollada como Proyecto de Fin de Grado para el ciclo de **Desarrollo de Aplicaciones Web**. 

---

## Despliegue en Producción

[![Desplegado en Railway](https://img.shields.io/badge/Railway-000000?style=for-the-badge&logo=railway&logoColor=white)](https://railway.com/)

> **Puedes acceder a la versión en vivo aquí:** [https://prueba1-tfg-production.up.railway.app/](https://prueba1-tfg-production.up.railway.app/)

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
* DB_HOST=padel-db
* DB_PORT=3306
* DB_DATABASE=padel_club
* DB_USERNAME=user_padel
* DB_PASSWORD=user_pass

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

### Puertos y Acceso
| Servicio | URL / Host | Puerto Externo | Puerto Interno |
| :--- | :--- | :--- | :--- |
| **Aplicación Web (Nginx)** | [http://localhost:8000](http://localhost:8000) | `8000` | `80` |
| **Base de Datos (MySQL)** | `127.0.0.1` | `3307` | `3306` |
| **Vite Dev Server** | `localhost` | `5173` | `5173` |
