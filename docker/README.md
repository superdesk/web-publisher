## How to build it with Docker:

#### Setup

Execute below commands inside `docker` folder of that repository.

#### Prerequisite

Copy `.env.dist` to `.env`:

```bash
cp .env.dist .env
```

`.env.dist` file contains default environment variables. 

#### Build

```bash
docker-compose build
```

#### Run containers

```bash
docker-compose up -d
```

#### Install all dependencies using Composer:

```bash
docker-compose run php php /usr/bin/composer install
```

#### Create database:

```bash
docker-compose run php php bin/console doctrine:database:create
```

#### Update database schema using Doctrine migrations:

```bash
docker-compose run php php bin/console doctrine:migrations:migrate
```

#### Load fixtures:

```bash
docker-compose run php php bin/console doctrine:fixtures:load
```

or 

```bash
docker-compose run php php -d memory_limit=-1 bin/console doctrine:fixtures:load
```

if the memory limit exceeded.

#### Install demo theme and its assets:

```bash
docker-compose run php php bin/console swp:theme:install 123abc src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f -p
docker-compose run php php bin/console sylius:theme:assets:install
```

#### Preview

Go to http://localhost:8080 for viewing the app in dev mode.

#### Configure (optional)

If you use Docker for Windows, you might need to additionally 
change the values of `SWP_DOMAIN` and `CACHE_SERVERS` env vars from `localhost` to `127.0.0.1` in `.env` file.

#### Where to see nginx logs?

`logs` dir will be created inside `docker/` dir. Nginx logs will be visible in `logs/nginx/` dir.

#### Where to see Publisher logs?

`logs` dir will be created inside `docker/` dir. Publisher logs will be visible in `logs/publisher/` dir.
