FROM debian:jessie

MAINTAINER Vincent Composieux <vincent.composieux@gmail.com>

VOLUME /var/www/webrenderer

RUN apt-get update && apt-get install -y php5-common php5-cli php5-fpm php5-mcrypt php5-mysql php5-apcu php5-gd php5-imagick php5-curl php5-intl curl git subversion

ADD webrenderer.ini /etc/php5/fpm/conf.d/
ADD webrenderer.ini /etc/php5/cli/conf.d/

RUN mkdir /usr/share/webrenderer
ADD docker-entrypoint.sh /usr/share/webrenderer/
RUN chmod -R 777 /usr/share/webrenderer

ADD webrenderer.pool.conf /etc/php5/fpm/pool.d/

RUN usermod -u 1000 www-data

ENTRYPOINT ["/usr/share/webrenderer/docker-entrypoint.sh"]

EXPOSE 9000
