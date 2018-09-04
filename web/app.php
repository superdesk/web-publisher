<?php

use Symfony\Component\HttpFoundation\Request;

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__.'/../vendor/autoload.php';
include_once __DIR__.'/../app/bootstrap.php.cache';

$kernel = new AppKernel('prod', false);

Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
Request::setTrustedProxies(['192.0.0.1', $request->server->get('REMOTE_ADDR')], Request::HEADER_X_FORWARDED_ALL);
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
