# How to install the Publisher with Docker

This guide installs the Publisher with Docker, ready to be used with Superdesk. 

To setup Superdesk with Docker for using it with the Publisher, see [Superdesk with Publisher plugin Docker installation guide](Superdesk.md). It is recommended to setup Superdesk first.

## Prerequisite

For using it with Superdesk, following line must be added the to `hosts` file on local machine, if it doesn't exist:

```
127.0.0.1  superdesk.local publisher.local
```


Both ```superdesk.local``` and ```publisher.local``` are configured as aliases for appropriate Superdesk and Publisher containers.

Before continuing, Docker network should be set, if it doesn't exist:

```bash
docker network create sp-publisher-network
```


Execute the commands below inside `etc/docker` folder of that repository.

```bash
cd etc/docker
```

## Prepare

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

`.env.example` file contains environment variable for having Publisher available at [publisher.local](http://publisher.local), and connected to Superdesk at [superdesk.local:8080](http://superdesk.local:8080) (available in superdesk-client Docker container). 

Copy `.docker-compose.yml.example` to `.docker-compose.yml.`

```bash
cp docker-compose.yml.example docker-compose.yml
```

## Build

```bash
docker-compose build
```

## Run containers

```bash
docker-compose up -d
```

## Install all dependencies using Composer

```bash
docker-compose run php php /usr/bin/composer install
```

## Create database

```bash
docker-compose run php php bin/console doctrine:database:create
```

## Update database schema using Doctrine migrations

```bash
docker-compose run php php bin/console doctrine:migrations:migrate --no-interaction 
```

## Tenants and organisation

There are two options:
* Load tenants and organization sample data fixtures
* Setup the tenant manually

### Load tenants and organization sample data with fixtures

If you want, you can install sample tenant data. Configuration is available in  [tenant.yml](src/SWP/Bundle/FixturesBundle/Resources/fixtures/ORM/dev/tenant.yml)

```bash
docker-compose run php php bin/console doctrine:fixtures:load --group=LoadTenantsData
```


#### Install demo theme and its assets for both loaded tenants

```bash
docker-compose run php php bin/console swp:theme:install 123abc src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f -p
docker-compose run php php bin/console swp:theme:install 456def src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f -p
docker-compose run php php bin/console sylius:theme:assets:install
```

Skip the next step (Setup the tenant manually)

### Setup the tenant manually

#### Create Elasticsearch indexes

```bash
docker-compose run php php bin/console fos:elastica:create
```

#### Create organization

```bash
docker-compose run php php bin/console swp:organization:create Publisher
```

Pay attention to **organization code** which will be needed in the next step.

#### Create tenant

```bash
docker-compose run php php bin/console swp:tenant:create
```

```
Please enter domain:publisher.local
Please enter subdomain: <skip if none>
Please enter name:SWP //(“Publisher” won’t work)
Please enter organization code: <organization code form the previous step>
```

Pay attention to the **tenant code** which will be needed in the next step.

#### Install theme

```bash
docker-compose run php php bin/console swp:theme:install <tenant code> src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f --activate
docker-compose run php php bin/console sylius:theme:assets:install
```

## Clear cache

```bash
docker-compose run php php bin/console cache:clear
```

## Set permissions for local image upload

If you plan to use local storage for asset upload, permissions should be set first:

```bash
docker exec docker_php_1 sh -c 'chown -R www-data:www-data /var/www/publisher/public/uploads'
```

## Preview

Go to http://localhost or http://publisher.local for viewing the app in dev mode.

## Configure (optional)

If you use Docker for Windows, you might need to additionally 
change the values of `SWP_DOMAIN` and `CACHE_SERVERS` env vars from `localhost` to `127.0.0.1` in `.env` file.

## Where to see nginx logs?

`logs` dir will be created inside `etc/docker/` dir. Nginx logs will be visible in `logs/nginx/` dir.

## Where to see Publisher logs?

`logs` dir will be created inside `etc/docker/` dir. Publisher logs will be visible in `logs/publisher/` dir.
