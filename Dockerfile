FROM php:8.2-apache

# ── Dependencias del sistema
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

# ── Extensiones PHP necesarias para Laravel + PostgreSQL
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    xml \
    bcmath \
    opcache

# ── Configurar OPCache para mejorar el rendimiento de Laravel
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.validate_timestamps=1'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.file_update_protection=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_file_override=0'; \
} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# ── Instalar Redis (extensión PHP)
RUN pecl install redis \
    && docker-php-ext-enable redis

# ── Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ── Directorio de trabajo
WORKDIR /var/www/html

# ── Configurar Apache y DocumentRoot
RUN a2enmod rewrite && \
    sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf

# ── Permisos para Laravel
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# ── Exponer puerto HTTP
EXPOSE 80