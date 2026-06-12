<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Routing;

use SWP\Bundle\ContentBundle\Model\ArticleSeoMediaInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Cmf\Component\Routing\VersatileGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Route as SymfonyRoute;

class SeoMediaRouter extends Router implements VersatileGeneratorInterface {

  const OBJECT_BASED_ROUTE_NAME = "__seo_media_router_route_name__";

  /**
   * {@inheritdoc}
   */
  public function generate(string $name, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string {
    $meta = null;
    if (self::OBJECT_BASED_ROUTE_NAME === $name && array_key_exists(RouteObjectInterface::ROUTE_OBJECT, $parameters)) {
      $meta = $parameters[RouteObjectInterface::ROUTE_OBJECT];
      unset($parameters[RouteObjectInterface::ROUTE_OBJECT]);
    }

    if (!$meta instanceof Meta || !$meta->getValues() instanceof ArticleSeoMediaInterface) {
      throw new RouteNotFoundException(sprintf('Route "%s" is not supported by this router.', $name));
    }

    $item = $meta->getValues()->getImage();

    $parameters['mediaId'] = $item->getAssetId();
    $parameters['extension'] = $item->getFileExtension();

    return parent::generate('swp_seo_media_get', $parameters, $referenceType);
  }

  public function getRouteDebugMessage(string $name, array $parameters = []): string {
    if (self::OBJECT_BASED_ROUTE_NAME === $name
        && array_key_exists(RouteObjectInterface::ROUTE_OBJECT, $parameters)
        && $parameters[RouteObjectInterface::ROUTE_OBJECT] instanceof Meta
    ) {
      return 'Route for media ' . $parameters[RouteObjectInterface::ROUTE_OBJECT]->getValues()->getId() . ' not found';
    }

    return 'Route "' . $name . '" not found';
  }
}
