FROM php:8.2-fpm

# Instalar dependencias necesarias para PHP y Laravel
RUN apt-get update && \
    apt-get install -y libssl-dev git unzip libcurl4-openssl-dev pkg-config nginx && \
    pecl install mongodb-1.15.0 && \
    docker-php-ext-enable mongodb && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configuración de Nginx
RUN rm /etc/nginx/sites-enabled/default
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copiar código de la aplicación
WORKDIR /var/www/html
COPY . .

# Instalar dependencias PHP
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer el puerto de Nginx
EXPOSE 8000

# Comando de inicio: Nginx + PHP-FPM
CMD ["/bin/sh", "-c", "service nginx start && php-fpm"]