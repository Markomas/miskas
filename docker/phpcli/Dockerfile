FROM php:8.0-cli

RUN apt-get update && apt-get install -y curl aria2

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get install -y \
        libzip-dev \
        zip \
  && docker-php-ext-install zip

COPY . /app
WORKDIR /app
