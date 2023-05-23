# How to install Superdesk with Docker, and connect it with the Publisher

This guide installs the Publisher with Docker, ready to be used with Superdesk. 

To setup Publisher with Docker for using it with Superdesk, see [Publisher Docker installation guide](README.md). It is recommended to setup Superdesk first.

## Prerequisite

For using it with the Publisher, following line must be added the to the ```hosts``` file on local machine, if not already set:

```
127.0.0.1  superdesk.local publisher.local
```

Both ```superdesk.local``` and ```publisher.local``` are configured as aliases for appropriate Superdesk and Publisher containers.

Before continuing, Docker network should be set, if it doesn't exist:

```bash
docker network create sp-publisher-network
```


## Install Superdesk

Clone Superdesk with the Publisher plugin repository and execute commands inside ```superdesk-sp``` directory:

``` bash
git clone -b docker https://github.com/superdesk/superdesk-sp
```
``` bash
cd superdesk-sp
```

Start superdesk using the ```docker-compose.yml``` file:

``` bash
docker-compose up -d
```

Finish Superdesk setup:

``` bash
docker-compose run superdesk-server /opt/superdesk/docker/start.sh
```

## Post installation 

This will install Superdesk with the Publisher plugin and make it available at [superdesk.local:8080](http://superdesk.local:8080). 

In order to have it talk to the Publisher, attach to ```superdesk-sp_superdesk-client_1``` container and edit ``config.random.js``` (ie 		config.ec23ae24.js).

```bash
docker exec -it superdesk-sp_superdesk-client_1 bash
apt update && apt -y install nano
```


```
window.superdeskConfig = {
	defaultTimezone: "Europe/Berlin",
	server: {
    	url: "http://superdesk.local:8080/api",
    	ws: "wss://superdesk.local:8080/ws"
	},
	iframely: {
    		key: "4358cefd09a66b1126c036"
	},
	raven: {
    		dsn: "https://17742b45c3ea48df9dabec80914aa6d2@sentry.sourcefabric.org/28"
	},
	apps: ['superdesk-publisher'],
	publisher: {
    	protocol: "http",
    	tenant: '',
    	domain: 'publisher.local',
    	base: "api/v2",
    	wsDomain: 'publisher.local',
    	wsPath: '/ws',
    	wsPort: '',
	},
};
```

