#!/bin/bash

if [ "$1" = 'composer' ]; then
    # install composer and install PHP dependencies
    if [ ! -f /var/www/webpublisher/composer.phar ]; then
        cd /var/www/webpublisher && curl -Ssk https://getcomposer.org/installer | php
    fi
    cd /var/www/webpublisher && /usr/bin/php composer.phar install
    #cd /var/www/webpublisher && /usr/bin/php composer.phar install --no-dev
    chown -R www-data:1000 /var/www/webpublisher

    /usr/sbin/php5-fpm -F

    echo "exited $0"
fi

exec "$@"
