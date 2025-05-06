FROM php:8.0-fpm-alpine

# Install necessary packages and PHP extensions
RUN apk update && \
    apk add --no-cache \
        supervisor \
        libmemcached-dev \
        zlib-dev \
        libzip-dev \
        postgresql-dev \
        rabbitmq-c-dev \
        git \
        $PHPIZE_DEPS && \
    docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        zip \
        opcache && \
    pecl install memcached amqp && \
    docker-php-ext-enable memcached amqp opcache

# Create supervisor configuration
RUN mkdir -p /etc/supervisor.d
RUN mkdir -p /var/log/supervisor

# Copy supervisor config files
COPY supervisor.ini /etc/supervisor.d/supervisor.ini
COPY supervisord.conf /etc/supervisord.conf

# Run supervisord
CMD ["supervisord", "-c", "/etc/supervisord.conf", "--nodaemon"]
