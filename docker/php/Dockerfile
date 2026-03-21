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

# CONFIGURACIÓN DE NGINX PARA LARAVEL
RUN echo 'server { \n\
    listen 80; \n\
    root /var/www/public; \n\
    add_header X-Frame-Options "SAMEORIGIN"; \n\
    add_header X-Content-Type-Options "nosniff"; \n\
    index index.php; \n\
    charset utf-8; \n\
    location / { try_files $uri $uri/ /index.php?$query_string; } \n\
    location ~ \.php$ { \n\
        fastcgi_pass 127.0.0.1:9000; \n\
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; \n\
        include fastcgi_params; \n\
    } \n\
}' > /etc/nginx/sites-available/default

WORKDIR /var/www

# COPIAR EL CÓDIGO
COPY ./src .

# INSTALAR DEPENDENCIAS DE PHP 
RUN composer install --no-dev --optimize-autoloader

# INSTALAR DEPENDENCIAS DE JS Y COMPILAR EL CSS/JS 
RUN npm install && npm run build

# CONFIGURAR PERMISOS 
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# SCRIPT DE INICIO (MIGRACIONES AUTOMÁTICAS + NGING + PHP)
RUN echo "#!/bin/sh\n\
php artisan migrate --force\n\
php-fpm -D\n\
nginx -g 'daemon off;'" > /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh

# VOLVER AL USUARIO WWW-DATA
USER www-data

EXPOSE 80
CMD ["/usr/local/bin/start.sh"]