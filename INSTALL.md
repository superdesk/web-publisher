## Docker installation:

See [detailed instructions here](docker/README.md).

## Installation:

#### 1. Clone Publisher repository and and follow the guide below.

#### 2. Install all dependencies:

```bash
composer install
```

#### 3. Create database

```bash
php app/console doctrine:database:create
```

#### 4. Populate database schema

```bash
php app/console doctrine:migrations:migrate
```

#### 5. Populate database with test data
 

```bash
php app/console doctrine:fixtures:load
```

or 

```bash
php -d memory_limit=-1 app/console doctrine:fixtures:load
```

#### 6. Install demo theme

```bash
php app/console swp:theme:install 123abc src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ -f -p
```


#### 7. Install theme assets:

```bash
php app/console sylius:theme:assets:install
```

#### 8. Run RabbitMQ consumers

For supervisor setup (and consumers managed by it) read instructions in `supervisor.md`

#### 9. Run WebSocket server:

```bash
php app/console gos:websocket:server
```

#### 10. Preview

Run project with built in php server:

```bash
php app/console server:start
```

Access the Superdesk Publisher in your browser at `http://localhost:8000`.
