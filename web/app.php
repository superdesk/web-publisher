<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\ClassLoader\XcacheClassLoader;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

require_once __DIR__.'/../app/AppKernel.php';
require_once __DIR__.'/../app/AppCache.php';

if (extension_loaded('apc') && ini_get('apc.enabled')) {
    $loader = new ApcClassLoader('superdesk_webpublisher', $loader);
    $loader->register(true);
}

if (extension_loaded('xcache') && ini_get('xcache.enabled')) {
    $loader = new XcacheClassLoader('superdesk_webpublisher', $loader);
    $loader->register(true);
}

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
$kernel = new AppCache($kernel);

Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
