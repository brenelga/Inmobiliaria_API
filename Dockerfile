FROM php:8.2-cli

# Instalar dependencias del sistema y extensión MongoDB
RUN apt-get update && \
    apt-get install -y libssl-dev git unzip && \
    pecl install mongodb && \
    docker-php-ext-enable mongodb

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Configurar el entorno
WORKDIR /app
COPY . .

# Instalar dependencias
RUN composer install --optimize-autoloader --no-dev

# Iniciar la aplicación
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=${PORT}"]