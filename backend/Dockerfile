FROM php:8.3-cli-alpine

RUN apk add --no-cache \
    icu-dev \
    git \
    unzip \
    && docker-php-ext-install \
    intl \
    pdo \
    pdo_mysql \
    opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /srv/app

COPY composer.json composer.lock* symfony.lock* ./

RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts

COPY . .

RUN mkdir -p var/cache var/log var/share config/jwt \
    && chown -R www-data:www-data /srv/app

EXPOSE 8000