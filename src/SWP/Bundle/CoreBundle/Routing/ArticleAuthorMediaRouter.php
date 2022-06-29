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
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route as SymfonyRoute;

class ArticleAuthorMediaRouter extends Router implements VersatileGeneratorInterface {
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

  public function generate($meta, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH) {
    if (RouteObjectInterface::OBJECT_BASED_ROUTE_NAME === $meta
        && array_key_exists(RouteObjectInterface::ROUTE_OBJECT, $parameters)
        && $parameters[RouteObjectInterface::ROUTE_OBJECT] instanceof SymfonyRoute
    ) {
      $meta = $parameters[RouteObjectInterface::ROUTE_OBJECT];
      unset($parameters[RouteObjectInterface::ROUTE_OBJECT]);
    }

    if ($meta instanceof Meta && ($meta->getValues() instanceof AuthorMediaInterface)) {
      return $this->authorMediaManager->getMediaPublicUrl($meta->getValues()->getImage());
    }

    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function supports($name) {
    return $name instanceof Meta && ($name->getValues() instanceof AuthorMediaInterface);
  }

  public function getRouteDebugMessage($name, array $parameters = []) {
    return 'Route for article author media ' . $name->getValues()->getId() . ' not found';
  }
}
