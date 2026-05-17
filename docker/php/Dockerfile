# Imagen base: PHP 8.3 con FPM (FastCGI Process Manager)
FROM php:8.3-fpm

# Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev

# Instala extensiones PHP que Laravel necesita
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instala Composer (gestor de dependencias PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crea usuario para evitar problemas de permisos
RUN useradd -G www-data,root -u 1000 -d /home/laravel laravel
RUN mkdir -p /home/laravel && chown -R laravel:laravel /home/laravel

# Directorio de trabajo dentro del contenedor
WORKDIR /var/www

# Cambia al usuario creado
USER laravel
