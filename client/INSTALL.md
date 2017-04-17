

We gonna use `ubuntu:trusty` as a base.



##### Install system-wide dependencies

Execute:

```sh
apt-get update && \
DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
libfreetype6 libfontconfig nodejs npm nginx git ca-certificates \
&& echo "\ndaemon off;" >> /etc/nginx/nginx.conf \
&& rm /etc/nginx/sites-enabled/default \
&& ln --symbolic /usr/bin/nodejs /usr/bin/node
```




Execute:

```sh
npm -g install grunt-cli bower
```




##### Setup the environment

Copy from the repository dir:

```sh
cp -r ./superdesk_vhost.conf /etc/nginx/sites-enabled/superdesk.conf
```



##### Install app-wide dependencies

Copy from the repository dir:

```sh
cp -r ./package.json /opt/superdesk-client/
```

Execute:

```sh
npm install
```


Copy from the repository dir:

```sh
cp -r ./bower.json /opt/superdesk-client/
cp -r ./.bowerrc /opt/superdesk-client/
```

Execute:

```sh
bower --allow-root install
```




##### Copy application sources

Copy from the repository dir:

```sh
cp -r . /opt/superdesk-client
```

-------

Working directory is: `/opt/superdesk-client/`

Following ports will be used by the application:

```sh
9000, 80
```

To start the application execute:

```sh
sh -c grunt build && nginx 
```
