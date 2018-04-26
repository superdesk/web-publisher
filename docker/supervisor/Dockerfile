FROM php:7.2-fpm-alpine3.7

RUN apk update

RUN set -ex \
    && apk --no-cache add \
        autoconf \
        build-base \
        libzmq \
        zeromq-dev \
        supervisor \
    && pecl install zmq-beta \
    && docker-php-ext-enable zmq

COPY supervisor.ini /etc/supervisor.d/supervisor.ini

CMD ["supervisord"]
