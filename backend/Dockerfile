FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    icu-dev \
    git \
    unzip \
    && docker-php-ext-install \
    intl \
    pdo \
    pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /srv/app

RUN chown -R 1000:1000 /srv/app