## How to build it with docker:

#### Setup

Add ```127.0.0.1 webpublisher.dev``` to ```/etc/hosts```. **Mac users** use the ip of your virtualbox (e.g. ```boot2docker ip```).

#### Build

```bash
docker-compose build
```

#### Run

```bash
docker-compose up
```

#### Configure

Make sure to adapt the following values in your configuration file:

```yaml
# app/config/parameters.yml
parameters:
    database_host: postgres
    database_user: postgres
```

The database with the name _symfony_ will be automatically created via Docker.

#### View

Go to http://webpublisher.dev/app_dev.php for viewing the app in dev mode.

### Updating database schema and creating the default tenant

Create database in your PostgreSQL server (it's required) manually. Remember to put database config into parameters.yml.

Then execute the following commands in terminal:

```bash
php app/console doctrine:schema:update --force
php app/console doctrine:phpcr:repository:init
php app/console swp:organization:create --default
php app/console swp:tenant:create --default
php app/console doctrine:phpcr:repository:init
```

Commands when using docker:

```bash
docker-compose run --rm php php /var/www/webpublisher/app/console doctrine:schema:update --force
docker-compose run --rm php php /var/www/webpublisher/app/console doctrine:phpcr:repository:init
docker-compose run --rm php php /var/www/webpublisher/app/console swp:organization:create --default
docker-compose run --rm php php /var/www/webpublisher/app/console swp:tenant:create --default
docker-compose run --rm php php /var/www/webpublisher/app/console doctrine:phpcr:repository:init
```

`swp:tenant:create --default` console command, creates a new, default tenant which is
needed to run the WebPublisher.

Alternatively, in the development environment, to populate the database with test data (including a default tenant), you can create the database and load fixtures with the following sequence of commands

```bash
php app/console doctrine:schema:update --force
php app/console doctrine:phpcr:repository:init
php app/console doctrine:phpcr:fixtures:load
php app/console doctrine:fixtures:load
```

You should also install a theme. To install our demo DefaultTheme - run following commands:

```bash
php app/console theme:setup -f
php app/console sylius:theme:assets:install
```
