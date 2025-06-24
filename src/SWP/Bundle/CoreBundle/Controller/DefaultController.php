<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use SWP\Bundle\ContentBundle\Factory\RouteFactoryInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Context\CachedTenantContextInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use FOS\ElasticaBundle\Elastica\Client as ElasticaClient;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DefaultController extends AbstractController {

  private CachedTenantContextInterface $tenantContext;
  private MetaFactoryInterface $metaFactory;
  private Context $templateEngineContext;
  private RouteFactoryInterface $routeFactory;

  public function __construct(
      CachedTenantContextInterface $tenantContext,
      MetaFactoryInterface         $metaFactory,
      Context                      $templateEngineContext,
      RouteFactoryInterface        $routeFactory
  ) {
    $this->tenantContext = $tenantContext;
    $this->metaFactory = $metaFactory;
    $this->templateEngineContext = $templateEngineContext;
    $this->routeFactory = $routeFactory;
  }

  /**
   * @Route("/", methods={"GET","POST"}, name="homepage")
   */
  public function indexAction(Request $request): Response {
    /** @var TenantInterface $currentTenant */
    $currentTenant = $this->tenantContext->getTenant();
    $route = $currentTenant->getHomepage();

    if (null === $route) {
      /** @var RouteInterface $route */
      $route = $this->routeFactory->create();
      $route->setStaticPrefix('/');
      $route->setName('Homepage');
      $route->setType('content');
      $route->setTemplateName('index.html.twig');
      $route->setCacheTimeInSeconds(360);
      $request->attributes->set(DynamicRouter::ROUTE_KEY, $route);
    }

    $this->templateEngineContext->setCurrentPage($this->metaFactory->create($route));

    $response = new Response();
    $response->headers->set('Content-Type', 'text/html; charset=UTF-8');

    return $this->render('index.html.twig', [], $response);
  }

  /**
   * @Route("/api/system/health", methods={"GET"}, name="system_health")
   * @IsGranted(null)
   */
  public function healthCheck(
      Connection $connection,
      ElasticaClient $elasticaClient,
      AdapterInterface $cachePool
  ): JsonResponse {
      $status = [
          'application_name' => 'Publisher',
          'postgres' => 'red',
          'elastic' => 'red',
          'memcached' => 'red',
          'rabbitmq' => 'red',
      ];

      // Check Postgres
      try {
          $connection->connect();
          if ($connection->isConnected()) {
              $status['postgres'] = 'green';
          }
      } catch (\Throwable $e) {}

      // Check Elasticsearch
      try {
          $elasticaClient->getStatus();
          $status['elastic'] = 'green';
      } catch (\Throwable $e) {}

      // Check Memcached (Symfony Cache)
      try {
          $cacheKey = 'health_check_' . uniqid();
          $cachePool->save($cachePool->getItem($cacheKey)->set('ok'));
          $cachePool->deleteItem($cacheKey);
          $status['memcached'] = 'green';
      } catch (\Throwable $e) {}

      // Check RabbitMQ using php-amqp extension
      try {
          $amqp = new \AMQPConnection([
              'host'     => $_ENV['RABBIT_MQ_HOST'] ?? 'localhost',
              'port'     => $_ENV['RABBIT_MQ_PORT'] ?? 5672,
              'login'    => $_ENV['RABBIT_MQ_USER'] ?? 'guest',
              'password' => $_ENV['RABBIT_MQ_PASSWORD'] ?? 'guest',
              'vhost'    => $_ENV['RABBIT_MQ_VHOST'] ?? '/',
          ]);
          $amqp->connect();
          if ($amqp->isConnected()) {
              $status['rabbitmq'] = 'green';
          }
      } catch (\Throwable $e) {}

      return new JsonResponse($status);
  }
}
