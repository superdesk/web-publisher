# Superdesk Bridge bundle
[![Build Status](https://travis-ci.org/SuperdeskWebPublisher/SWPBridgeBundle.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/SWPBridgeBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SuperdeskWebPublisher/SWPBridgeBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SuperdeskWebPublisher/SWPBridgeBundle/?branch=master)
[![Code Climate](https://codeclimate.com/github/SuperdeskWebPublisher/SWPBridgeBundle/badges/gpa.svg)](https://codeclimate.com/github/SuperdeskWebPublisher/SWPBridgeBundle)

This bundle is a bridge between the Superdesk Content API and the Superdesk Web Publisher.

## Installation

1. Download SWPBridgeBundle
2. Enable the bundle and its dependencies
3. Import SWPUpdaterBundle routing file
4. Configure the SWPBridgeBundle

### Step 1: Install SWPBridgeBundle with Composer

Run the following composer require command:

``` bash
$ php composer.phar require swp/bridge-bundle
```

### Step 2: Enable the bundle and its dependencies

Enable the bundle in `AppKernel.php`.

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new SWP\Bundle\BridgeBundle\SWPBridgeBundle()
    );
}
```

### Step 3: Import SWPBridgeBundle routing file

You have to import SWPBridgeBundle routing file. You can use YAML or XML format.

YAML:

``` yaml
# app/config/routing.yml
swp_bridge:
    resource: "@SWPBridgeBundle/Resources/config/routing.yml"
    prefix:   /
```

### Step 4: Configure the SWPBridgeBundle

Add the following parameters to your `config.yml` file.

```yaml
# app/config/config.yml
swp_bridge:
    api:
        host: 'example.com'
    auth:
        client_id: 'my_client_id'
        username: 'my_username'
        password: 'my_password'
```

This is a minimum requirement to get started, see the section 
[Everything about configuration](#everything-about-configuration) for more details.

## Everything about configuration

### Configuration reference

```yaml
swp_bridge:
    api:
        host: 'The hostname of your Content API instance. This option is required.'
        port: 'The port of your Content API Instance, defaults to 80.'
        protocol: 'Protocol to use to connect to your Content API Instance, options are: _http_ and _https_. Defaults to _http_.'
    auth:
        client_id: 'Client ID for authenticating with your Content API Instance. This option is required.'
        username: 'Username for authenticating with your Content API Instance. This option is required.'
        password: 'Password for authenticating with your Content API Instance. This option is required.'
    options: 'An array of options which will included in each call in GuzzleApiClient->makeApiCall(). The values defined here will override values from the Request object if the keys are identical.'
```

### Adding custom http client options:

SWPBridgeBundle uses Guzzle to fetch data from the external server. You can add
custom Guzzle options / headers for your http client by simply adding an array
of options as a parameter in your configuration.  
The example below shows how to add custom curl options.

```yaml
# app/config/config.yml
swp_bridge:
    options:
        curl: # http://guzzle.readthedocs.org/en/latest/faq.html#how-can-i-add-custom-curl-options
            10203: # integer value of CURLOPT_RESOLVE
                 - "example.com:5050:localhost"  # This will resolve the host example.com to your localhost 
```

For more details see [Guzzle documentation](http://guzzle.readthedocs.org/en/latest/request-options.html).

At this stage, the bundle is ready to be used by your application.

#### Development Configuration

The above example is specific for the Guzzle client and allows you to do custom
hostname resolving, practical when using docker in your devevelopment environment.
Just add ```127.0.0.1    publicapi``` to your hosts file and change the value 
_localhost_ to the ip address of you publicapi docker container.
