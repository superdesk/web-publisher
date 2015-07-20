#!/bin/bash

if [ "$1" = 'composer' ]; then
    # install composer and install PHP dependencies
    if [ ! -f /var/www/webrenderer/composer.phar ]; then
        cd /var/www/webrenderer && curl -Ss https://getcomposer.org/installer | php
    fi
    cd /var/www/webrenderer && /usr/bin/php composer.phar install
    #cd /var/www/webrenderer && /usr/bin/php composer.phar install --no-dev
    chown -R www-data:1000 /var/www/webrenderer

    /usr/sbin/php5-fpm -F

    echo "exited $0"
fi

exec "$@"
