## General upgrade instructions

On our internal infrastructure we use [deployphp/deployer](https://github.com/deployphp/deployer) project for deployments/upgrades. 
Here is an example of the configuration for the Superdesk Publisher: https://gist.github.com/ahilles107/b6dd85f67282a41d81f273fe12344b0e

Most important steps to do after updating code:

* Check release notes for new versions in this file
* Run database migrations: `doctrine:migrations:migrate` command
* Clear doctrine cache with `doctrine:cache:clear main_cache` and `doctrine:cache:clear-metadata` commands
* Install themes assets with `sylius:theme:assets:install` command
* (optionally) Clear memcached store (with `echo \'flush_all\' | nc localhost 11211` on ubuntu)

## Release 1.4.0

* [BC] Article author `avatarUrl` property is deprecated. It will be removed in 1.5 version. Use `avatar` instead. 

## Release 1.3.0

* [BC] RabbitMqSupervisorBundle was removed 

In order to have RabbitMq consumers running, programs fot them need to be added manually to supervisor config. 
Read how to do this in `supervisor.md` file.