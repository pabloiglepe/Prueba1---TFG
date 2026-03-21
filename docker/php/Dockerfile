FROM php:8.2-fpm

# INSTALAR DEPENDENCIAS DEL SISTEMA Y NODE.JS
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# LIMPIAR CACHÉ DEL SISTEMA
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# INSTALAR EXTENSIONES DE PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# INSTALAR COMPOSER
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# CONFIGURAR DIRECTORIO DE TRABAJO
WORKDIR /var/www

# CONFIGURACIÓN DE NGINX
COPY ./docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# COPIAR EL CÓDIGO DE LARAVEL
COPY ./src .

# INSTALAR DEPENDENCIAS DE PHP 
RUN composer install --no-dev --optimize-autoloader

# INSTALAR DEPENDENCIAS DE JS Y COMPILAR EL CSS/JS 
RUN npm install && npm run build

# CONFIGURAR PERMISOS 
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# SCRIPT DE INICIO (MIGRACIONES AUTOMÁTICAS + NGING + PHP)
RUN printf "#!/bin/sh\n\
php artisan migrate --force\n\
php artisan db:seed --force\n\
/usr/local/sbin/php-fpm -D\n\
/usr/sbin/nginx -g 'daemon off;'" > /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh

# VOLVER AL USUARIO WWW-DATA
USER www-data

EXPOSE 80
CMD ["/usr/local/bin/start.sh"]