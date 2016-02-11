FROM debian:jessie

MAINTAINER Vincent Composieux <vincent.composieux@gmail.com>

VOLUME /var/www/webpublisher

RUN apt-get update && apt-get install -y php5-common php5-cli php5-fpm php5-mcrypt php5-mysql php5-pgsql php5-apcu php5-gd php5-imagick php5-curl php5-intl curl git subversion

ADD webpublisher.ini /etc/php5/fpm/conf.d/
ADD webpublisher.ini /etc/php5/cli/conf.d/

RUN mkdir /usr/share/webpublisher
ADD docker-entrypoint.sh /usr/share/webpublisher/
RUN chmod -R 777 /usr/share/webpublisher

ADD webpublisher.pool.conf /etc/php5/fpm/pool.d/

RUN usermod -u 1000 www-data

ENTRYPOINT ["/usr/share/webpublisher/docker-entrypoint.sh"]

EXPOSE 9000
