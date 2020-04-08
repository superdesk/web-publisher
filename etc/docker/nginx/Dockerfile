FROM nginx:latest

COPY default.conf /etc/nginx/conf.d/
COPY nginx.conf /etc/nginx/nginx.conf

RUN rm -f /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Berlin /etc/localtime
