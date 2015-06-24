FROM debian:jessie

RUN apt-get update && apt-get install -y curl php5-cli php5-curl

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

ADD . /var/www/webrenderer
WORKDIR /var/www/webrenderer

RUN mkdir /usr/share/webrenderer
ADD docker-entrypoint.sh /usr/share/webrenderer/
RUN chmod -R 777 /usr/share/webrenderer

ENTRYPOINT ["/usr/share/webrenderer/docker-entrypoint.sh"]
