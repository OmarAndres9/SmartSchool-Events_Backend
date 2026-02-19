FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    build-essential \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    git \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    xml \
    curl \
    bcmath \
    opcache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY Backend/ .

# Copiar configuración de PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/laravel.ini

# Instalar dependencias de PHP
RUN composer install --optimize-autoloader --no-dev

# Dar permisos correctos
RUN chown -R www-data:www-data /var/www/html

# Exponer puerto
EXPOSE 9000

CMD ["php-fpm"]
