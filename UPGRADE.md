## General upgrade instructions

On our internal infrastructure we use [deployphp/deployer](https://github.com/deployphp/deployer) project for deployments/upgrades.
Here is an example of the configuration for the Superdesk Publisher:
```
<?php

namespace Deployer;

use function sprintf;

require __DIR__.'/vendor/deployer/deployer/recipe/symfony4.php';

set('repository', 'https://github.com/superdesk/web-publisher.git');
set('ssh_type', 'native');
set('ssh_multiplexing', true);
set('writable_use_sudo', true);
set('writable_mode', 'chown');
set('keep_releases', 8);



set('composer_options', static function () {
    $options = '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader';

    return 'prod' !== get('symfony_env') ? $options : sprintf('%s --no-dev', $options);
});

set('console_options', function () {
    $options = '--no-interaction --env={{symfony_env}}';

    return 'prod' !== get('symfony_env') ? $options : sprintf('%s --no-debug', $options);
});

// ENV
set('env', static function () {
    return ['APP_ENV' => has('symfony_env') ? get('symfony_env') : 'prod'];
});

// SHARED
set('shared_files', [
    '.env.local',
    '.env.local.php',
    'config/jwt/private.pem',
    'config/jwt/public.pem',
    'config/gcs/google_credentials.json',
]); // reset sharing .env

add('writable_dirs', ['public/uploads', 'public/bundles', 'app/themes']);
add('shared_dirs', ['public/uploads', 'public/bundles', 'app/themes']);

inventory('hosts.yml');

// CUSTOM TASKS
desc('Clear main cache (metadata, twig, internal optimizations)');
task('doctrine:cache', static function () {
    run('{{bin/php}} {{bin/console}} cache:clear {{console_options}}');
});

desc('Install themes assets');
task('theme:assets:install', static function () {
    run('{{bin/php}} {{bin/console}} sylius:theme:assets:install {{console_options}}');
});

desc('Rollback database');
task('rollback:db', static function () {
    run('{{bin/php}} {{bin/console}} doctrine:migrations:migrate prev {{console_options}}');
});

desc('Clean up after 1.x to 2.x migration');
task('deploy:publisher:migrate', static function () {
    run('mkdir -p {{deploy_path}}/shared/public');
    run('sudo mv {{deploy_path}}/shared/web/uploads  {{deploy_path}}/shared/public/');
    run('sudo mv {{deploy_path}}/shared/web/bundles  {{deploy_path}}/shared/public/');
});

task('deploy:assets:install', static function () {
    run('{{bin/php}} {{bin/console}} assets:install {{console_options}} {{release_path}}/web');
})->desc('Install bundle assets');

task('deploy:user-permissions', static function () {
    run('umask 0002');
});


// TASKS ORDER
before('deploy:lock', 'deploy:user-permissions');
before('deploy:symlink', 'database:migrate');
before('deploy:symlink', 'theme:assets:install');
before('deploy:cache:warmup', 'doctrine:cache');
after('deploy:assets:install', 'theme:assets:install');

after('rollback', 'rollback:db');
after('deploy:failed', 'deploy:unlock');
```

Most important steps to do after updating code:

* Check release notes for new versions in this file
* Run database migrations: `doctrine:migrations:migrate` command
* Clear doctrine cache with `doctrine:cache:clear main_cache` and `doctrine:cache:clear-metadata` commands
* Install themes assets with `sylius:theme:assets:install` command
* Install project assets with `assets:install` command
* (optionally) Clear memcached store (with `echo \'flush_all\' | nc localhost 11211` on ubuntu)

## Release 2.4

* If you are upgrading from an instance using `swp_article.extra` run this after the upgrade:

```
php bin/console swp:migration:fill-article-extra
```

## Release 2.1

* [BC BREAK] Removed OldSoundRabbitMqBundle.
* [BC BREAK] Removed `rabbitmq:consumer content_push` command. Use `messenger:consume async_content_push` instead.
* [BC BREAK] Removed `rabbitmq:consumer analytics_event` command. Use `messenger:consume async_analytics_event` instead.
* [BC BREAK] Removed `rabbitmq:consumer image_conversion` command. Use `messenger:consume async_image_conversion` instead.
* [BC BREAK] Removed `rabbitmq:consumer send_webhook` command. Use `messenger:consume async_webhooks` instead.
* [BC BREAK] Removed `SWP\Bundle\CoreBundle\Consumer\AnalyticsEventConsumer`. Use `SWP\Bundle\CoreBundle\MessageHandler\AnalyticsEventHandler` instead.
* [BC BREAK] Removed `SWP\Bundle\CoreBundle\Consumer\ContentPushConsumer`. Use `SWP\Bundle\CoreBundle\MessageHandler\ContentPushHandler` instead.
* [BC BREAK] Removed `SWP\Bundle\CoreBundle\Consumer\ImageConversionConsumer`. Use `SWP\Bundle\CoreBundle\MessageHandler\ImageConversionHandler` instead.
* [BC BREAK] Removed `SWP\Bundle\CoreBundle\Consumer\SendWebhookConsumer`. Use `SWP\Bundle\CoreBundle\MessageHandler\WebhookHandler` instead.
* [BC BREAK] Removed `old_sound_rabbit_mq.content_push_producer` service. Use `messenger.default_bus` instead.
* [BC BREAK] Added `SWP\Bundle\CoreBundle\MessageHandler\ContentPushMigrationHandler` class to handle separate content push migration tasks.
* [BC BREAK] Added new Supervisor config. See `supervisor.md` for more details.
* [BC BREAK] Added new env variable `MESSENGER_TRANSPORT_DSN` to `.env` file.
* [BC BREAK] Removed `RABBITMQ_URL` env variable from `.env.` file.

## Release 2.0

* [BC] ```Unrecognized options "containers, widgets" under "sylius_theme.generatedData". Available options are "contentLists", "menus", "routes".```
From your theme config file (`theme.json`) remove nodes: `generatedData.containers` and `generatedData.widgets`
* [BC] Config values are moved from `app/config/parameters.yml` to `.env.local`. Default values were moved from `app/config/parameters.yml.dist` to `.env`
* [BC] `app/console` file was moved to `bin/console`
* [BC] `app/logs` directory was renamed to `var/logs`
* [BC] `web/` directory was renamed to `public/`
* New API version is now `v2`
* In server vhost config change references to `app.php` into `index.php`. And there is no more `app_dev.php` - activate dev mode with env variables changes.

## Release 1.4.0

* [BC] Article author `avatarUrl` property is deprecated. It will be removed in 1.5 version. Use `avatar` instead.

## Release 1.3.0

* [BC] RabbitMqSupervisorBundle was removed

In order to have RabbitMq consumers running, programs fot them need to be added manually to supervisor config.
Read how to do this in `supervisor.md` file.
