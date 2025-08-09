FROM php:8.2-cli

# Instalar dependencias del sistema
RUN apt-get update && \
    apt-get install -y libssl-dev git unzip libcurl4-openssl-dev pkg-config

# Instalar extensión MongoDB
RUN pecl install mongodb-1.15.0 && \
    docker-php-ext-enable mongodb

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Configurar el entorno
WORKDIR /app
COPY . .

# Instalar dependencias ANTES del CMD
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs

ENV PORT=8000
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=$((${PORT:-8000}))"]