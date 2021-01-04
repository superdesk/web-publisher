FROM php:7.4-fpm-alpine

RUN apk update
RUN apk add curl-dev

RUN set -ex \
    && apk --no-cache add \
        postgresql-dev \
        zlib-dev \
        libxml2-dev \
        freetype \
        libpng \
        libjpeg-turbo \
        freetype-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        libintl \
        icu \
        icu-dev \
        git \
        libmemcached-dev \
        autoconf \
        build-base \
        rabbitmq-c-dev \
        libzip-dev \
        zip \
        oniguruma-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-install opcache \
    && docker-php-ext-install sockets \
    && pecl install memcached \
    && docker-php-ext-enable memcached \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install exif \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip xml gd mbstring curl bcmath

RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

# Set timezone
RUN rm -f /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Berlin /etc/localtime
RUN "date"

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER 1
# create composer cache directory
RUN mkdir -p /var/www/.composer && chown -R www-data /var/www/.composer

WORKDIR /var/www/publisher
