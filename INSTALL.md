## Docker installation:

See [detailed instructions here](etc/docker/README.md).

## Installation:

#### 1. Clone Publisher repository and and follow the guide below.

#### 2. Install all dependencies:

```bash
composer install
```

#### 3. Create database

```bash
php bin/console doctrine:database:create
```

#### 4. Populate database schema

```bash
php bin/console doctrine:migrations:migrate
```

#### 5. Populate database with test data
 

```bash
php bin/console doctrine:fixtures:load
```

or 

```bash
php -d memory_limit=-1 bin/console doctrine:fixtures:load
```


#### 6. Generate the SSH keys to properly use the authentication (readers):

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

#### 7. Install demo theme

```bash
php bin/console swp:theme:install 123abc src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f -p
```


#### 8. Install theme assets:

```bash
php bin/console sylius:theme:assets:install
```

#### 9. Run RabbitMQ consumers

For supervisor setup (and consumers managed by it) read instructions in `supervisor.md`

#### 10. Run WebSocket server:

```bash
php bin/console gos:websocket:server
```

or it can be started using [Supervisor](supervisor.md#running-websocket-server).

#### 11. Preview

Run project with built in php server:

```bash
php bin/console server:start
```

Access the Superdesk Publisher in your browser at `http://localhost:8000`.
