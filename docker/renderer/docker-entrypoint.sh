#!/bin/bash

chown -R www-data:1000 /var/www/webrenderer
composer install
ls -al

echo "exited $0"


exec "$@"

while true; do sleep 1000; done

