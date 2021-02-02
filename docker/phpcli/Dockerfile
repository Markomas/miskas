FROM php:8.0-cli

RUN apt-get update && apt-get install -y curl aria2

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get install -y \
        libzip-dev \
        zip \
  && docker-php-ext-install zip

RUN \
    docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure mysqli --with-mysqli=mysqlnd \
    && docker-php-ext-install pdo_mysql

RUN pecl install redis \
    && docker-php-ext-enable redis

COPY . /app
WORKDIR /app
