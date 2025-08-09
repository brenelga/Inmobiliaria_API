FROM php:8.2-cli

# Instalar dependencias
RUN apt-get update && \
    apt-get install -y libssl-dev git unzip libcurl4-openssl-dev pkg-config && \
    pecl install mongodb-1.15.0 && \
    docker-php-ext-enable mongodb && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
COPY . .

RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs

# Solución definitiva para el puerto
ENV PORT=8000
RUN echo '<?php passthru("php artisan serve --host=0.0.0.0 --port=".(int)($_ENV["PORT"]??8000));' > /start.php
CMD ["php", "/start.php"]