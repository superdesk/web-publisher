Superdesk Web Publisher
======================

[![Build Status](https://travis-ci.org/superdesk/web-publisher.svg?branch=master)](https://travis-ci.org/superdesk/web-publisher)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/superdesk/web-publisher/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/superdesk/web-publisher/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/56bc97382a29ed00396b3760/badge.svg?style=flat)](https://www.versioneye.com/user/projects/56bc97382a29ed00396b3760)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c1d40e6d-f4c3-42fa-af0e-d4a4e521d435/mini.png)](https://insight.sensiolabs.com/projects/c1d40e6d-f4c3-42fa-af0e-d4a4e521d435)

Superdesk Web Publisher - the next generation publishing platform for journalists and newsrooms.

## Documentation

Full documentation can be found here: [http://superdesk-web-publisher.readthedocs.org][1]


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


[1]: http://superdesk-web-publisher.readthedocs.org/en/latest/

## Testing

For unit tests use PHPSpec tests, for functional tests use PHPUnit and Behat for integration.

How to run tests?

```bash
php bin/phpunit -c app/ # PHPUnit
php bin/phpspec run # PHPSpec
```

To see current code tests coverage run:

For PHPSpec copy`phpspec.yml.dist` to `phpspec.yml` and uncomment:

```yaml
#extensions:
#    - PhpSpec\Extension\CodeCoverageExtension

#code_coverage:
#    output: build/coverage
#    format: html
```

and re-run PHPSpec.

For PHPUnit:

```
php bin/phpunit -c app/ --coverage-text
```

Send code coverage raport to [codecov.io](https://codecov.io/github/superdesk/web-publisher) with:

```
bash <(curl -s https://codecov.io/bash) -t 9774e0ee-fd3e-43d3-8ba6-a25e4ef57fe5
```

**Note:** remember to enable `Xdebug` to generate the coverage.

License
-----------

See the complete license [here](LICENSE.md).

Contributors
-------

This component is a Sourcefabric z.ú. and contributors initiative.

List of all authors and contributors can be found [here](AUTHORS.md).

## Superdesk Web Publisher is possible thanks to other Sourcefabric initiatives:

* [swp/templates-system](https://github.com/SuperdeskWebPublisher/templates-system) [![Build Status](https://travis-ci.org/SuperdeskWebPublisher/templates-system.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/templates-system) [![Code Climate](https://codeclimate.com/github/SuperdeskWebPublisher/templates-system/badges/gpa.svg)](https://codeclimate.com/github/SuperdeskWebPublisher/templates-system) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SuperdeskWebPublisher/templates-system/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SuperdeskWebPublisher/templates-system/?branch=master)
* [swp/multi-tenancy](https://github.com/SuperdeskWebPublisher/multi-tenancy) [![Build Status](https://travis-ci.org/SuperdeskWebPublisher/multi-tenancy.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/multi-tenancy) [![Code Climate](https://codeclimate.com/github/SuperdeskWebPublisher/multi-tenancy/badges/gpa.svg)](https://codeclimate.com/github/SuperdeskWebPublisher/multi-tenancy) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SuperdeskWebPublisher/multi-tenancy/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SuperdeskWebPublisher/multi-tenancy/?branch=master) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/34801a37-b258-4fbf-b395-7ae004218334/mini.png)](https://insight.sensiolabs.com/projects/34801a37-b258-4fbf-b395-7ae004218334)
