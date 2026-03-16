FROM php:8.2-fpm

# INSTALAR DEPENDENCIAS DEL SISTEMA
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# LIMPIAR CACHÉ DEL SISTEMA
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# INSTALAR EXTENSIONES DE PHP NECESARIAS PARA LARAVEL Y MYSQL 
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# INSTALAR GESTOR DE DEPEDENCIAS (COMPOSER)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# CREAR USUARIO DEL SISTEMA PARA EVITAR CONFLICTO DE PERMISOS 
RUN useradd -G www-data,root -u 1000 -d /home/dev dev
RUN mkdir -p /home/dev/.composer && \
    chown -R dev:dev /home/dev

WORKDIR /var/www

USER dev