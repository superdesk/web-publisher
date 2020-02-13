## General upgrade instructions

On our internal infrastructure we use [deployphp/deployer](https://github.com/deployphp/deployer) project for deployments/upgrades. 
Here is an example of the configuration for the Superdesk Publisher: https://gist.github.com/ahilles107/b6dd85f67282a41d81f273fe12344b0e

Most important steps to do after updating code:

* Check release notes for new versions in this file
* Run database migrations: `doctrine:migrations:migrate` command
* Clear doctrine cache with `doctrine:cache:clear main_cache` and `doctrine:cache:clear-metadata` commands
* Install themes assets with `sylius:theme:assets:install` command
* Install project assets with `assets:install` command
* (optionally) Clear memcached store (with `echo \'flush_all\' | nc localhost 11211` on ubuntu)

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
