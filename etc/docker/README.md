# How to install Superdesk and Publisher with Docker

This guide explains how to install complete Superdesk digital publishing system made of Superdesk headless CMS (with the Publisher plugin) and the Publisher, using Docker. 

## Prerequisites

For using Superdesk and Publisher tohetger, the following line must be added the to `hosts` file on local machine, if it doesn't exist:

```
127.0.0.1  superdesk.local publisher.local
```


Both ```superdesk.local``` and ```publisher.local``` are configured as aliases for appropriate Superdesk and Publisher containers.

Before continuing, Docker network should be set, if it doesn't exist:

```bash
docker network create sp-publisher-network
```


## Superdesk

Clone Superdesk with the Publisher plugin repository 

``` bash
git clone -b docker https://github.com/superdesk/superdesk-sp
```

Replace the contents of ```docker-compose.yml``` with the following:

```
version: "3.2"
services:

  mongodb:
    image: mongo:4

  redis:
    image: redis:3

  elastic:
    image: docker.elastic.co/elasticsearch/elasticsearch:7.10.1
    environment:
      - discovery.type=single-node

  superdesk-server:
    build: ./server
    depends_on:
      - redis
      - mongodb
      - elastic
    environment:
      - SUPERDESK_URL=http://superdesk.local:8080/api
      - DEMO_DATA=1 # install demo data, set to 0 if you want clean install
      - WEB_CONCURRENCY=2
      - SUPERDESK_CLIENT_URL=http://superdesk.local:8080
      - CONTENTAPI_URL=http://superdesk.local:8080/capi
      - MONGO_URI=mongodb://mongodb/superdesk
      - CONTENTAPI_MONGO_URI=mongodb://mongodb/superdesk_capi
      - PUBLICAPI_MONGO_URI=mongodb://mongodb/superdesk_papi
      - LEGAL_ARCHIVE_URI=mongodb://mongodb/superdesk_legal
      - ARCHIVED_URI=mongodb://mongodb/superdesk_archive
      - ELASTICSEARCH_URL=http://elastic:9200
      - ELASTICSEARCH_INDEX=superdesk
      - CELERY_BROKER_URL=redis://redis:6379/1
      - REDIS_URL=redis://redis:6379/1
      - DEFAULT_TIMEZONE=Europe/Prague
      - SECRET_KEY=secretkey
      # More configuration options can be found at https://superdesk.readthedocs.io/en/latest/settings.html

  superdesk-client:
    build: ./client
    environment:
      # If not hosting on localhost, change these lines
      - SUPERDESK_URL=http://superdesk.local:8080/api
      - SUPERDESK_WS_URL=ws://superdesk.local:8080/ws
      - IFRAMELY_KEY
    depends_on:
      - superdesk-server
    ports:
      - "8080:80"
    networks:
      default:
        aliases:
          - superdesk.local

networks:
    default:
      external:
        name: sp-publisher-network
```

If you want to use AWS S3 or Google Cloud Storage, add the following lines to ```superdesk-server``` container environment in ```docker-compose.yml```:

```
      - AMAZON_SERVE_DIRECT_LINKS=True
      - AMAZON_ENDPOINT_URL=
      - AMAZON_ACCESS_KEY_ID=
      - AMAZON_SECRET_ACCESS_KEY=
      - AMAZON_REGION=auto
      - AMAZON_CONTAINER_NAME=
```

Execute commands inside ```superdesk-sp``` directory:


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

### Post installation 

This will install Superdesk with the Publisher plugin and make it available at [superdesk.local:8080](http://superdesk.local:8080). 

In order to have it talk to the Publisher, attach to ```superdesk-sp_superdesk-client_1``` container and edit `config.random.js` (ie config.ec23ae24.js).

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

### Restarting containers
After the first start, Superdesk is being populated with demo data, which is generally good idea. However, reinserting demo data will create problems, so after the first ```docker-compose up -d``` run, change demo data environment variable in ```docker-compose.yml``` to ```0``` for the ```superdesk-server``` container:

```
- DEMO_DATA=0
```


## Publisher

If you are still in the `superdesk-sp` directory, go one step up
```
cd ..
```

Clone Publisher repository and move into the `etc/docker` directory 

``` bash
git clone -b 2.4 https://github.com/superdesk/web-publisher
cd web-publisher/etc/docker
```

### Prepare

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

`.env.example` file contains environment variable for having Publisher available at [publisher.local](http://publisher.local), and connected to Superdesk at [superdesk.local:8080](http://superdesk.local:8080) (available in superdesk-client Docker container). 

Copy `.docker-compose.yml.example` to `.docker-compose.yml.`

```bash
cp docker-compose.yml.example docker-compose.yml
```

### Build

```bash
docker-compose build
```

### Run containers

```bash
docker-compose up -d
```

### Install all dependencies using Composer

```bash
docker-compose run php php /usr/bin/composer install
```

### Create database

```bash
docker-compose run php php bin/console doctrine:database:create
```

### Update database schema using Doctrine migrations

```bash
docker-compose run php php bin/console doctrine:migrations:migrate --no-interaction 
```

### Tenants and organisation

There are two options:
* Load tenants and organization sample data fixtures
* Setup the tenant manually

#### Load tenants and organization sample data with fixtures

If you want, you can install sample tenant data. Configuration is available in  [tenant.yml](src/SWP/Bundle/FixturesBundle/Resources/fixtures/ORM/dev/tenant.yml)

```bash
docker-compose run php php bin/console doctrine:fixtures:load --group=LoadTenantsData
```


##### Install demo theme and its assets for both loaded tenants

```bash
docker-compose run php php bin/console swp:theme:install 123abc src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f -p
docker-compose run php php bin/console swp:theme:install 456def src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f -p
docker-compose run php php bin/console sylius:theme:assets:install
```

Skip the next step (Setup the tenant manually)

#### Setup the tenant manually

##### Create Elasticsearch indexes

```bash
docker-compose run php php bin/console fos:elastica:create
```

##### Create organization

```bash
docker-compose run php php bin/console swp:organization:create Publisher
```

Pay attention to **organization code** which will be needed in the next step.

##### Create tenant

```bash
docker-compose run php php bin/console swp:tenant:create
```

```
Please enter domain:publisher.local
Please enter subdomain: <leave blank>
Please enter name:SWP //(“Publisher” won’t work)
Please enter organization code: <organization code form the previous step>
```

Pay attention to the **tenant code** which will be needed in the next step.

##### Install theme

```bash
docker-compose run php php bin/console swp:theme:install <tenant code> src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f --activate
docker-compose run php php bin/console sylius:theme:assets:install
```

### Clear cache

```bash
docker-compose run php php bin/console cache:clear
```

### Set permissions for local image upload and cache dir

If you plan to use local storage for asset upload, permissions should be set first:

```bash
docker exec docker_php_1 sh -c 'chown -R www-data:www-data /var/www/publisher/public/uploads'
```

```bash
docker exec docker_php_1 sh -c 'chown -R www-data:www-data /var/www/publisher/var/cache'
```

### Preview

Go to http://publisher.local for viewing the app in dev mode.

### Configure (optional)

If you use Docker for Windows, you might need to additionally 
change the values of `SWP_DOMAIN` and `CACHE_SERVERS` env vars from `localhost` to `127.0.0.1` in `.env` file.

### Where to see nginx logs?

`logs` dir will be created inside `etc/docker/` dir. Nginx logs will be visible in `logs/nginx/` dir.

### Where to see Publisher logs?

`logs` dir will be created inside `etc/docker/` dir. Publisher logs will be visible in `logs/publisher/` dir.

## Setting up Superdesk to talk to Publisher

After successfully finishing both installations, there are additional steps in order to have articles being published from Superdesk to Publisher. 

First of all, check if Superdesk and Publisher are talking to each other, assuming that the Publisher is installed and available at [publisher.local](http://publisher.local): go to the hamburger menu in the upper left corner, and choose Publisher Settings, Publisher tenant(s) should be listed.
 
### Create product

https://www.youtube.com/watch?v=g_lVOJ5aOzQ

* Product Type: Both

### Create Subscriber

In order to push content from Superdesk to Publisher, a Subscriber must be added with following information (replicate the setup from the video):

**Destination**
* Resource URL: http://publisher.local/api/v2/content/push
* Assest URL: http://publisher.local/api/v2/assets/push

https://www.youtube.com/watch?v=wjsVBM88IRg (**this video uses the older version with publisher.local:8080, use publisher.local instead**)

### Create “catch all” publishing rule

After the content is sent to Publisher’s subscriber, Publisher needs a generic rule to “catch” and publish it. Therefore one “catch all” rule should be created.

In *[Publisher Settings](http://superdesk.local:8080/#/publisher/settings)* (available in the Superdesk "hamburger" menu) choose *Publishing Rules* from the left pane, click *Add new* and choose *Organizational rule*.  Name it *catch-all*, switch on *Catch all* toggle, click + button under *Destination (Tenants)*, choose your tenant and click *Save*.


### Create and publish an article

https://www.youtube.com/watch?v=UDCZdEfGfHI

This process can be automated by creating publishing rules with category targeting (specific category publishes to specific route). Article flow from Superdesk to Publisher  can be monitored in the Publisher Output Control.
