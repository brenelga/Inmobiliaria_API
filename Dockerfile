FROM php:8.2-fpm

RUN apt-get update && apt-get install -y nginx git unzip libssl-dev libcurl4-openssl-dev pkg-config \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY composer.json composer.lock /var/www/html/
RUN composer install --optimize-autoloader --no-dev || cat /root/.composer/composer.log
COPY . /var/www/html

COPY . .

RUN chown -R www-data:www-data storage bootstrap/cache

COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 8000

CMD ["/bin/sh", "-c", "service nginx start && php-fpm"]
