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
    nginx \
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
# COPY ./docker/nginx/default.conf /etc/nginx/sites-available/default
COPY ./docker/nginx/default.railway.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
# COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf


# COPIAR EL CÓDIGO DE LARAVEL
COPY ./src .


# INSTALAR DEPENDENCIAS DE PHP 
RUN composer install --no-dev --optimize-autoloader


# BORRAMOS EL ARCHIVO 'public/hot'
RUN rm -rf public/hot
RUN rm -rf public/build

# INSTALAR DEPENDENCIAS DE JS Y COMPILAR EL CSS/JS 
RUN npm install 
RUN npm run build

# CONFIGURAR PERMISOS 
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage /var/www/bootstrap/cache /var/www/public


# SCRIPT DE INICIO (MIGRACIONES AUTOMÁTICAS + NGING + PHP)
# ENTRYPOINT ["/bin/sh", "-c", "php artisan config:clear && php artisan route:clear && php artisan view:clear && php-fpm -D && nginx -g 'daemon off;'"]
ENTRYPOINT ["/bin/sh", "-c", "php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan migrate --force && php-fpm -D && nginx -g 'daemon off;'"]


USER root
EXPOSE 80
