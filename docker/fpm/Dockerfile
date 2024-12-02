ARG node_20_version=20.4.0-alpine

FROM scratch AS config_files

COPY ./docker/fpm/default_php.ini /config/php/conf.d/z-98-default-php.ini
COPY ./docker/fpm/prod_php.ini /config/php/conf.d/z-99-prod-php.ini
COPY ./docker/fpm/www-pool-default.conf /config/php-fpm.d/zz-www-01.conf
COPY ./docker/fpm/www-pool-prod.conf /config/php-fpm.d/zz-www-02.conf

FROM php:8.2.9-fpm-alpine AS prod_base

RUN apk update && \
    apk add --upgrade --no-cache \
        curl \
        libcurl \
        git \
        su-exec \
        fcgi \
        postgresql-libs \
        libzip \
        icu && \
    apk add --no-cache --virtual .build-only-deps \
        postgresql-dev \
        shadow \
        libzip-dev \
        icu-dev && \
    usermod -u 1000 www-data && \
    groupmod -g 1000 www-data && \
    docker-php-ext-install \
        pdo \
        intl \
        zip \
        pdo_pgsql \
        pdo_mysql && \
    docker-php-ext-enable \
        opcache && \
    apk del .build-only-deps shadow

ENV COMPOSER_HOME /var/www/.composer
COPY --from=composer/composer:2.5.8-bin /composer /usr/bin/composer
RUN mkdir -p  /var/www/.composer \
    && chown www-data:www-data /var/www/.composer

FROM node:${node_20_version} AS prod_assets

WORKDIR /app

COPY ./assets/package.json ./assets/yarn.lock ./assets/

RUN yarn install --cwd="./assets" && mkdir ./public

COPY ./assets/js ./assets/js
COPY ./assets/scss ./assets/scss
COPY ./assets/styles ./assets/styles
COPY ./assets/images ./assets/images
COPY ./assets/webpack.config.js ./assets/webpack.config.js

RUN yarn run --cwd="./assets" encore production

FROM scratch AS backend_files

WORKDIR /app

COPY ./phinx.php ./.env ./docker/fpm/.env.local ./
#COPY ./tasks ./tasks
COPY ./bin ./bin
COPY ./db ./db
COPY ./packages ./packages
COPY ./config ./config
COPY ./resources ./resources
COPY ./src ./src

FROM prod_base AS prod_last

WORKDIR /var/www/html

COPY ./composer.json ./
COPY ./config/preload.php ./config/preload.php

RUN APP_ENV=prod composer install --no-autoloader --no-interaction --prefer-dist --no-dev --no-scripts \
    && composer clear-cache

COPY ./public ./public
COPY --from=backend_files /app ./
COPY --from=prod_assets /app/public ./public

RUN composer dump-autoload -a --no-dev \
    && COMPOSER_MEMORY_LIMIT=128M APP_ENV=prod APP_DEBUG=0 su-exec www-data:www-data composer run app:prod-warmup \
    && chown -R www-data:www-data /var/www/html/var/
