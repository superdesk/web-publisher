## Docker installation (recommended)

We **strongly recommend** using Docker for local installation and testing.

As it works with our in-house headless newsroom management system, Superdesk, we provide [the instructions](etc/docker/Superdesk.md) how to install Superdesk equipped with Publisher integration.

After Superdesk installation, Publisher [should follow](etc/docker/README.md).

## Installation from source

This is the installation procedure using the code from the repository, assuming that all [requierments](REQUIREMENTS.md) are met.

Keep in mind:
* Document root for Nginx vhost should be [public](public/) directory.
* PostgreSQL must accept local connections with user ```postgres:postgres```


Clone Publisher repository and and follow the guide below.

### Prerequisite

Copy `.env.local.example` to `.env.local`:

```bash
cp .env.local.example .env.local
```

### Install all dependencies:

```bash
composer install
```

### Create database

```bash
php bin/console doctrine:database:create
```

### Populate database schema

```bash
php bin/console doctrine:migrations:migrate
```

### Tenants and organisation

There are two options:
* Load tenants and organization sample data fixtures
* Setup the tenant manually

#### Load tenants and organization sample data with fixtures

If you want, you can install sample tenant data. Configuration is available in  [tenant.yml](src/SWP/Bundle/FixturesBundle/Resources/fixtures/ORM/dev/tenant.yml)

```bash
php bin/console doctrine:fixtures:load --group=LoadTenantsData
```


##### Install demo theme and its assets for both loaded tenants

```bash
php bin/console swp:theme:install 123abc src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f -p
php bin/console swp:theme:install 456def src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f -p
php bin/console sylius:theme:assets:install
```

Skip the next step (Setup the tenant manually)

#### Setup the tenant manually

##### Create Elasticsearch indexes

```bash
php bin/console fos:elastica:create
```

##### Create organization

```bash
php bin/console swp:organization:create Publisher
```

Pay attention to **organization code** which will be needed in the next step.

##### Create tenant

```bash
php bin/console swp:tenant:create
```

```
Please enter domain:publisher.local
Please enter subdomain: <skip if none>
Please enter name:SWP //(“Publisher” won’t work)
Please enter organization code: <organization code form the previous step>
```

Pay attention to the **tenant code** which will be needed in the next step.

##### Install theme

```bash
php bin/console swp:theme:install <tenant code> src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f --activate
php bin/console sylius:theme:assets:install
```


#### Generate the SSH keys to properly use the authentication (readers)

Generate the SSH keys:


``` bash
$ mkdir -p config/jwt
$ openssl genrsa -out config/jwt/private.pem -aes256 4096
$ openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

In case first ```openssl``` command forces you to input password use following to get the private key decrypted
``` bash
$ openssl rsa -in config/jwt/private.pem -out config/jwt/private2.pem
$ mv config/jwt/private.pem config/jwt/private.pem-back
$ mv config/jwt/private2.pem config/jwt/private.pem
```


#### Run RabbitMQ consumers

For supervisor setup (and consumers managed by it) read instructions in `supervisor.md`

#### Run WebSocket server:

```bash
php bin/console gos:websocket:server
```

or it can be started using [Supervisor](supervisor.md#running-websocket-server).

#### Preview

http://localhost
