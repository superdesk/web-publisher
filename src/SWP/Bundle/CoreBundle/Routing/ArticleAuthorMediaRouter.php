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

namespace SWP\Bundle\CoreBundle\Routing;

use Psr\Log\LoggerInterface;
use SWP\Bundle\ContentBundle\Model\AuthorMediaInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Cmf\Component\Routing\VersatileGeneratorInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route as SymfonyRoute;

class ArticleAuthorMediaRouter extends Router implements VersatileGeneratorInterface {

  const OBJECT_BASED_ROUTE_NAME = "__article_author_media_router_route_name__";

  protected $authorMediaManager;

  public function __construct(
      ContainerInterface $container,
                         $resource,
      array              $options = [],
      RequestContext     $context = null,
      ContainerInterface $parameters = null,
      LoggerInterface    $logger = null,
      string             $defaultLocale = null
  ) {
    $this->authorMediaManager = $container->get('swp_core_bundle.manager.author_media');

    parent::__construct($container, $resource, $options, $context, $parameters, $logger, $defaultLocale);
  }

  public static function getSubscribedServices(): array {
    return array_merge(parent::getSubscribedServices(), [
        'swp_core_bundle.manager.author_media' => \SWP\Bundle\ContentBundle\Manager\MediaManagerInterface::class,
    ]);
  }

  public function generate(string $name, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string {
    $meta = null;
    if (self::OBJECT_BASED_ROUTE_NAME === $name
        && array_key_exists(RouteObjectInterface::ROUTE_OBJECT, $parameters)
    ) {
      $meta = $parameters[RouteObjectInterface::ROUTE_OBJECT];
      unset($parameters[RouteObjectInterface::ROUTE_OBJECT]);
    }

    if ($meta instanceof Meta && ($meta->getValues() instanceof AuthorMediaInterface)) {
      return $this->authorMediaManager->getMediaPublicUrl($meta->getValues()->getImage());
    }

    throw new RouteNotFoundException(sprintf('Route "%s" is not supported by this router.', $name));
  }

  public function getRouteDebugMessage(string $name, array $parameters = []): string {
    if (self::OBJECT_BASED_ROUTE_NAME === $name
        && array_key_exists(RouteObjectInterface::ROUTE_OBJECT, $parameters)
        && $parameters[RouteObjectInterface::ROUTE_OBJECT] instanceof Meta
    ) {
      return 'Route for article author media ' . $parameters[RouteObjectInterface::ROUTE_OBJECT]->getValues()->getId() . ' not found';
    }

    return 'Route "' . $name . '" not found';
  }
}
