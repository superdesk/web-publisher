Superdesk Web Renderer
======================

[![Build Status](https://travis-ci.org/superdesk/web-renderer.svg?branch=master)](https://travis-ci.org/superdesk/web-renderer)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/superdesk/web-renderer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/superdesk/web-renderer/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/556eccea663430000a300100/badge.svg?style=flat)](https://www.versioneye.com/user/projects/556eccea663430000a300100)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/63e28e0c-a2f4-40ec-bf8f-79a5ce8bd3e7/small.png)](https://insight.sensiolabs.com/projects/63e28e0c-a2f4-40ec-bf8f-79a5ce8bd3e7)

WebPublisher is templates engine + articles/pages render component for Superdesk WebPublisher. Thanks to Web Renderer, templators can design themes and work with templates engine.

## Documentation

Full documentation can be found here: [http://web-renderer.readthedocs.org/][1]

## How to build it with docker:

#### Setup

Add ```127.0.0.1 webrenderer.dev``` to ```/etc/hosts```. **Mac users** use the ip of your virtualbox (e.g. ```boot2docker ip```).

#### Build:

```bash
docker-compose build
```

#### Run:

```bash
docker-compose up
```

#### Updating database schema

Create database in your PostgreSQL server (it's required) manually. Remember to put database config into parameters.yml.

Then execute the following commands in terminal:

```bash
php app/console doctrine:phpcr:init:dbal --force
php app/console doctrine:phpcr:repository:init
php app/console doctrine:schema:update --force
```

[1]: http://web-renderer.readthedocs.org/

## Testing

For unit tests use PHPSpec tests, for functional tests use PHPUnit and Behat for integration.

How to run tests?

```bash
php bin/phpunit -c app/ # PHPUnit
php bin/phpspec run # PHPSpec
```

To see current code tests coverage run:
```
php bin/phpspec run --config=spec/phpspec-cov-html.yml
php bin/phpunit -c app/ --coverage-text
```

## Superdesk Web Renderer is possible thanks to other Sourcefabric initiatives:

* [swp/templates-system](https://github.com/SuperdeskWebPublisher/templates-system) [![Build Status](https://travis-ci.org/SuperdeskWebPublisher/templates-system.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/templates-system) [![Code Climate](https://codeclimate.com/github/SuperdeskWebPublisher/templates-system/badges/gpa.svg)](https://codeclimate.com/github/SuperdeskWebPublisher/templates-system) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SuperdeskWebPublisher/templates-system/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SuperdeskWebPublisher/templates-system/?branch=master)

