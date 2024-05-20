# How to install Superdesk and Publisher using Docker

This guide explains how to install a complete Superdesk digital publishing system made of Superdesk headless CMS (with the Publisher plugin) and the Publisher, using Docker.

## Prerequisites

For using Superdesk and Publisher together, the following line must be added the to the `hosts` file on the local machine, if it doesn't exist:

```
127.0.0.1  superdesk.local publisher.local
```


Both ```superdesk.local``` and ```publisher.local``` are configured as aliases for respective Superdesk and Publisher containers.

Before continuing, the Docker network should be set, if it doesn't exist:

```bash
docker network create sp-publisher-network
```


## Superdesk

Clone Superdesk with the Publisher plugin repository:

``` bash
git clone -b release/2.7 https://github.com/superdesk/superdesk-sp
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

Execute commands inside the ```superdesk-sp``` directory:


``` bash
cd superdesk-sp
```

```docker-compose.yml``` file contains an environment for having the Superdesk at [superdesk.local:8080](http://superdesk.local:8080) (```superdesk-client``` container), and available to the Publisher by using the ```sp-publisher-network``` network created earlier. In case you are trying a different setup, set it up before moving on.

Start Superdesk using the ```docker-compose.yml``` file:

``` bash
docker compose up -d
```

Finish Superdesk setup:

``` bash
docker compose run superdesk-server /opt/superdesk/docker/start.sh
```

After the first start, the Superdesk gets populated with the demo data, which is generally a good idea. However, reinserting demo data will create problems, so after the first ```docker compose up -d``` run, change the ```DEMO_DATA``` environment variable in ```docker-compose.yml``` to ```0``` for the ```superdesk-server``` container:

```
- DEMO_DATA=0
```


### Post installation

This will install Superdesk with the Publisher plugin and make it available at [superdesk.local:8080](http://superdesk.local:8080).

To have it talk to the Publisher, attach to ```superdesk-sp-superdesk-client-1``` container and edit `config.random.js` (ie config.ec23ae24.js).

```bash
docker exec -it superdesk-sp-superdesk-client-1 bash
```

```bash
apt update && apt -y install nano && nano config.ec23ae24.js
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
	apps: ['superdesk-publisher','superdesk-planning'],
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
This concludes the Superdesk installation and setup, and the Publisher connection. As the Publisher is not yet installed and configured, it won't be available in Superdesk, if you try looking for it at this point. The steps for installing the Publisher are below.


## Publisher

If you are still in the `superdesk-sp` directory, go one step up
```
cd ..
```

Clone the Publisher repository and move into the `etc/docker` directory

``` bash
git clone -b v2.4.2 https://github.com/superdesk/web-publisher
```

``` bash
cd web-publisher/etc/docker
```

### Prepare

```.env``` file contains an environment variable for having the Publisher available at [publisher.local](http://publisher.local), and connected to Superdesk at [superdesk.local:8080](http://superdesk.local:8080) (available in superdesk-client Docker container). In case you are trying a different setup, set it up before moving on.

The same apples to `docker-compose.yml`. There is also a `docker-compose.yml.dev` with a more developer oriented setup.

### Build

```bash
docker compose build
```

### Run containers

```bash
docker compose up -d
```

### Install all dependencies using Composer

```bash
docker compose run php php /usr/bin/composer install
```

### Create database

```bash
docker compose run php php bin/console doctrine:database:create
```

### Update database schema using Doctrine migrations

```bash
docker compose run php php bin/console doctrine:migrations:migrate --no-interaction
```

### Tenants and organization

There are two options for creating organization and tenants:
* Load sample data fixtures
* Manual setup

#### Load tenants and organization sample data with fixtures

If you want, you can install sample tenant data. Configuration is available in  [tenant.yml](src/SWP/Bundle/FixturesBundle/Resources/fixtures/ORM/dev/tenant.yml)

```bash
docker compose run php php bin/console doctrine:fixtures:load --group=LoadTenantsData
```


##### Install demo theme and its assets for both loaded tenants

```bash
docker compose run php php bin/console swp:theme:install 123abc src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f -p
```

```bash
docker compose run php php bin/console swp:theme:install 456def src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f -p
```

```bash
docker compose run php php bin/console sylius:theme:assets:install
```

Skip the next step (Setup the tenant manually)

#### Manual setup

##### Create Elasticsearch indexes

```bash
docker compose run php php bin/console fos:elastica:create
```

##### Create organization

```bash
docker compose run php php bin/console swp:organization:create Publisher
```

Pay attention to **organization code** which will be needed in the next step.

##### Create tenant

```bash
docker compose run php php bin/console swp:tenant:create
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
docker compose run php php bin/console swp:theme:install <tenant code> src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f --activate
```

```bash
docker compose run php php bin/console sylius:theme:assets:install
```

### Clear cache

```bash
docker compose run php php bin/console cache:clear
```

### Set permissions for local log, image upload and cache dir

If you plan to use local storage for asset upload, permissions should be set first:

```bash
docker exec docker-php-1 sh -c 'chown -R www-data:www-data /var/www/publisher/public/uploads'
```

```bash
docker exec docker-php-1 sh -c 'chown -R www-data:www-data /var/www/publisher/var/cache'
```

```bash
docker exec docker-php-1 sh -c 'chown -R www-data:www-data /var/www/publisher/var/log'
```

### Preview

Go to http://publisher.local to view the app in dev mode.

### Optional configuration

If you use Docker for Windows, you might need to additionally
change the values of `SWP_DOMAIN` and `CACHE_SERVERS` env vars from `localhost` to `127.0.0.1` in the `.env` file.

### Where to see nginx logs?

`logs` dir will be created inside `etc/docker/` dir. Nginx logs will be visible in `logs/nginx/` dir.

### Where to see Publisher logs?

`logs` dir will be created inside `etc/docker/` dir. Publisher logs will be visible in `logs/publisher/` dir.

## Setting up Superdesk to talk to Publisher

After successfully finishing both installations, there are additional steps to have articles published from the Superdesk to the Publisher.

First of all, check if the Superdesk and Publisher are talking to each other, assuming that the Publisher is installed and available at [publisher.local](http://publisher.local): go to the hamburger menu in the upper left corner of the Superdesk interface, and choose Publisher Settings.

Publisher tenant(s) should be listed.

### Create product

https://www.youtube.com/watch?v=g_lVOJ5aOzQ

* Product Type: Both

### Create Subscriber

To push content from the Superdesk to the Publisher, a Subscriber must be added with the following information (replicate the setup from the video):

**Destination**
* Resource URL: http://publisher.local/api/v2/content/push
* Asset URL:

https://www.youtube.com/watch?v=wjsVBM88IRg (**this video uses the older version with publisher.local:8080, use publisher.local instead**)

### Create a “catch all” publishing rule

After the content is sent to the Publisher’s subscriber, the Publisher needs a generic rule to “catch” and publish it. Therefore one “catch all” rule should be created.

In *[Publisher Settings](http://superdesk.local:8080/#/publisher/settings)* (available in the Superdesk "hamburger" menu) choose *Publishing Rules* from the left pane, click *Add new* and choose *Organizational rule*.  Name it *catch-all*, switch on *Catch all* toggle, click + button under *Destination (Tenants)*, choose your tenant, and click *Save*.


### Create and publish an article

https://www.youtube.com/watch?v=UDCZdEfGfHI

This process can be automated by creating publishing rules with category targeting (specific category publishes to the specific route). Article flow from Superdesk to Publisher  can be monitored in the Publisher Output Control.
