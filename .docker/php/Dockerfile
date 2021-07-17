FROM php:8.0.8-fpm
WORKDIR /app

RUN apt-get update && apt-get install --yes git zip && apt-get clean
RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php && chmod 755 composer.phar && mv composer.phar /usr/local/bin/composer
