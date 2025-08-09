FROM php:8.2-cli

# Instalar dependencias del sistema
RUN apt-get update && \
    apt-get install -y libssl-dev git unzip libcurl4-openssl-dev pkg-config

# Instalar extensión MongoDB (versión específica)
RUN pecl install mongodb-1.15.0 && \
    docker-php-ext-enable mongodb

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Configurar el entorno
WORKDIR /app
COPY . .

# Instalar dependencias
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs

# Configuración final
COPY start.sh /start.sh
RUN chmod +x /start.sh
CMD ["php", "server.php"]