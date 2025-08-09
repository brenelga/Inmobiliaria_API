FROM php:8.2-cli

# Instalar dependencias del sistema
RUN apt-get update && \
    apt-get install -y libssl-dev git unzip libcurl4-openssl-dev pkg-config

# Instalar versión específica de MongoDB extension (1.21.0)
RUN pecl install mongodb-1.21.0 && \
    docker-php-ext-enable mongodb

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Configurar el entorno
WORKDIR /app
COPY . .

# Instalar dependencias (ignorando requisitos de plataforma temporalmente)
RUN rm -rf composer.lock && \
    composer install --optimize-autoloader --no-dev --ignore-platform-reqs
# Iniciar la aplicación
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=${PORT}"]