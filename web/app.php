<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\ClassLoader\XcacheClassLoader;
use Symfony\Component\HttpFoundation\Request;

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__.'/../app/autoload.php';
include_once __DIR__.'/../app/bootstrap.php.cache';

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
// Disable AppCache until there will be good solution for https://github.com/FriendsOfSymfony/FOSHttpCacheBundle/issues/276
//$kernel = new AppCache($kernel);

Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
