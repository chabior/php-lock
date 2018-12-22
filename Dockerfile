FROM php:7.3-cli


COPY . /var/www/php-lock
WORKDIR /var/www/php-lock

RUN apt-get update && \
    apt-get install curl git zip -y && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


RUN composer install --no-interaction