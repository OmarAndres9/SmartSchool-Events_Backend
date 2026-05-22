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

# ── Configurar OPCache
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

# ── Redis
RUN pecl install redis \
    && docker-php-ext-enable redis

# ── Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ── Directorio de trabajo
WORKDIR /var/www/html

# ── Copiar proyecto
COPY . /var/www/html

# ── Crear carpetas necesarias ANTES de composer install
RUN mkdir -p \
    /var/www/html/bootstrap/cache \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ── Instalar dependencias
RUN composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction

# ── Apache (rewrite + proxy/load balancer headers)
RUN a2enmod rewrite remoteip && \
    { \
      echo 'RemoteIPHeader X-Forwarded-For'; \
      echo 'RemoteIPInternalProxy 10.0.0.0/8'; \
      echo 'RemoteIPInternalProxy 172.16.0.0/12'; \
      echo 'RemoteIPInternalProxy 192.168.0.0/16'; \
    } >> /etc/apache2/apache2.conf && \
    sed -ri 's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

EXPOSE 80