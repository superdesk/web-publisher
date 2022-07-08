<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Twig;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Model\ArticleSeoMediaInterface;
use SWP\Bundle\ContentBundle\Model\AuthorMediaInterface;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Routing\MediaRouter;
use SWP\Bundle\ContentBundle\Routing\SeoMediaRouter;
use SWP\Bundle\CoreBundle\Routing\ArticleAuthorMediaRouter;
use SWP\Bundle\CoreBundle\Routing\MetaRouter;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class DecoratingRoutingExtension extends AbstractExtension {
  private RoutingExtension $routingExtension;

  public function __construct(RoutingExtension $routingExtension) {
    $this->routingExtension = $routingExtension;
  }

  public function getPath($name, $parameters = [], $relative = false): ?string {
    if ($name == null) {
      return null;
    }

    if (is_object($name)) {
      $object = $name;
      $name = null;
      self::setupParams($object, $name, $parameters);
    }

    try {
      return $this->routingExtension->getPath($name, $parameters, $relative);
    } catch (RouteNotFoundException|MissingMandatoryParametersException|InvalidParameterException $e) {
      // allow empty path
    }

    return null;
  }

  public function getUrl($name, $parameters = [], $schemeRelative = false): ?string {
    if ($name == null) {
      return null;
    }

    if (is_object($name)) {
      $object = $name;
      $name = null;
      self::setupParams($object, $name, $parameters);
    }

    try {
      return $this->routingExtension->getUrl($name, $parameters, $schemeRelative);
    } catch (RouteNotFoundException|MissingMandatoryParametersException|InvalidParameterException $e) {
      // allow empty url
    }

    return null;
  }

  private static function setupParams(object $object, &$name, &$parameters) {
    $name = RouteObjectInterface::OBJECT_BASED_ROUTE_NAME;
    $parameters[RouteObjectInterface::ROUTE_OBJECT] = $object;

    if ($object instanceof Meta) {
      $values = $object->getValues();
      if (($values instanceof ArticleMediaInterface || $values instanceof ImageRenditionInterface)) {
        $name = MediaRouter::OBJECT_BASED_ROUTE_NAME;
        return;
      }

      if ($values instanceof ArticleSeoMediaInterface) {
        $name = SeoMediaRouter::OBJECT_BASED_ROUTE_NAME;
        return;
      }

      if ($values instanceof AuthorMediaInterface) {
        $name = ArticleAuthorMediaRouter::OBJECT_BASED_ROUTE_NAME;
        return;
      }

      if ($values instanceof ArticleInterface || $values instanceof RouteInterface) {
        $name = MetaRouter::OBJECT_BASED_ROUTE_NAME;
        return;
      }
    }

    if ($object instanceof RouteInterface || $object instanceof ArticleInterface) {
      $name = MetaRouter::OBJECT_BASED_ROUTE_NAME;
    }
  }

  public function getFunctions(): array {
    return [
        new TwigFunction('url', [$this, 'getUrl'], ['is_safe_callback' => [$this->routingExtension, 'isUrlGenerationSafe']]),
        new TwigFunction('path', [$this, 'getPath'], ['is_safe_callback' => [$this->routingExtension, 'isUrlGenerationSafe']]),
    ];
  }
}
