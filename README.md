Superdesk Web Renderer
======================

WebPublisher is templates engine + articles/pages render component for Superdesk WebPublisher. Thanks to Web Renderer, templators can design themes and work with templates engine.

## Documentation

Full documentation can be found here: [http://web-renderer.readthedocs.org/][1]

### Web Renderer contains:

 * templates engine based on twig
 * plugins system
 * rest api for content

### main features:

* content storage for articles
* provide plugins system for WebPublisher and Webdesk (with rest api)


### technology stack

* PHP >- 5.5
* Symfony >= 2.8
* Twig 
* Behat
* PhpSpec
* postgresql
* elasticsearch

## How to build it with docker:

#### Build php and nginx images:

```bash
docker build -t webrenderer/php-fpm docker/php-fpm/
docker build -t webrenderer/nginx docker/nginx/
```

add ```webrenderer.dev``` to ```/etc/hosts``` (with ip of your virtualbox machine: ```boot2docker ip```).

#### Run:

```docker-compose up```

[1]: http://web-renderer.readthedocs.org/

## Testing

For unit tests use PHPSpec tests, for functional tests use PHPUnit and Behat for integration.

How to run tests?

```bash
phpunit -c /app # PHPUnit tests (functional)
php vendor/bin/phpspec run # PHPSpec tests
```