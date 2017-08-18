## How to build it with docker (development purposes):

#### Setup

Execute below commands inside `docker` folder of that repository.

#### Build

```bash
docker-compose build
```

#### Run

```bash
docker-compose up -d
```

#### Pre-configuration

Make sure to adapt the following values in your configuration file:

```yaml
# app/config/parameters.yml
parameters:
    database_host: postgres
    database_user: postgres
```

#### Install

Install all dependencies:

```bash
docker-compose run php composer install
```

#### Post-configuration

Create database:

```bash
docker-compose run php app/console doctrine:database:create
```

Update database schema using Doctrine migrations:

```bash
docker-compose run php app/console doctrine:migrations:migrate
```

Load fixtures:

```bash
docker-compose run php app/console doctrine:fixtures:load
```

Install demo theme:

```bash
docker-compose run php app/console swp:theme:install 123abc src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f
docker-compose run php app/console sylius:theme:assets:install
```

#### Preview

Go to http://localhost:8080/app_dev.php for viewing the app in dev mode.

## Local installation:

Clone Publisher repository and and follow the guide below.

Install all dependencies:

```bash
composer install
```

### Updating database schema and creating the default tenant

Execute the following commands in terminal:

```bash
php app/console doctrine:migrations:migrate
php app/console swp:organization:create --default
php app/console swp:tenant:create --default
```

`swp:tenant:create --default` console command, creates a new, default tenant which is
needed to run the Publisher.

Alternatively, in the development environment, to populate the database with test data (including a default tenant), you can create the database and load fixtures with the following sequence of commands

```bash
php app/console doctrine:migrations:migrate
php app/console doctrine:fixtures:load
```

You should also install a theme. To install our demo DefaultTheme - run following commands:

In case of loaded development data:

```bash
php app/console swp:theme:install 123abc src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f
```

else:

```bash
php app/console swp:theme:install <tenant> src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f
```

where `<tenant>` is tenant's code generated when a new tenant is created.

The last step is to install assets:

 
```bash
php app/console sylius:theme:assets:install
```

#### Preview

Run project with built in php server:

```bash
php app/console server:start publisher.dev
```
Replace publisher.dev with your `domain` parameter value configured on composer installation.