FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev unzip && docker-php-ext-install zip

WORKDIR /var/www/html

COPY composer.json ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

COPY . .

RUN mkdir -p uploads/neutral uploads/anxious && chmod -R 777 uploads
