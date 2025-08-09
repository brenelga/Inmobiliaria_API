FROM php:8.2-fpm

# Instalar dependencias del sistema y extensiones PHP necesarias para Laravel
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    && pecl install mongodb-1.15.0 \
    && docker-php-ext-enable mongodb \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Configurar Nginx
RUN rm /etc/nginx/sites-enabled/default
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copiar código de la aplicación
WORKDIR /var/www/html
COPY . .

# Instalar dependencias PHP de Laravel
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer el puerto dinámico de Railway
ENV PORT=8000
EXPOSE 8000

# Comando de inicio: iniciar Nginx y PHP-FPM
CMD ["/bin/sh", "-c", "service nginx start && php-fpm"]
