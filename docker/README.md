## How to build it with Docker:

#### Setup

Execute below commands inside `docker` folder of that repository.

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
docker-compose run php composer install
```

#### Configure

Make sure to adapt the following values in your configuration file:

```yaml
# app/config/parameters.yml
parameters:
    # ...
    env(DATABASE_HOST): postgres
    env(DATABASE_PORT): null
    env(DATABASE_NAME): publisher
    env(DATABASE_USER): postgres
    env(DATABASE_PASSWORD): postgres
    # ...
    env(ELASTICA_HOST): elasticsearch
```

#### Create database:

```bash
docker-compose run php app/console doctrine:database:create
```

#### Update database schema using Doctrine migrations:

```bash
docker-compose run php app/console doctrine:migrations:migrate
```

#### Load fixtures:

```bash
docker-compose run php app/console doctrine:fixtures:load
```

or 

```bash
docker-compose run php php -d memory_limit=-1 app/console doctrine:fixtures:load
```

if the memory limit exceeded.

#### Install demo theme and its assets:

```bash
docker-compose run php app/console swp:theme:install 123abc src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f -p
docker-compose run php app/console sylius:theme:assets:install
```

#### Preview

Go to http://localhost:8080/app_dev.php for viewing the app in dev mode.

*Note* If you use Docker for Windows, you might need to additionally 
change the values of `env(SWP_DOMAIN)` and `cache_servers` params from `localhost` to `127.0.0.1`.
