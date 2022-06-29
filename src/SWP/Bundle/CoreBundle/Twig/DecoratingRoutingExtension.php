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

use Symfony\Bridge\Twig\Extension\RoutingExtension;
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

    if(is_object($name)) {
      $name = $name->__toString();
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

    if(is_object($name)) {
      $name = $name->__toString();
    }

    try {
      return $this->routingExtension->getUrl($name, $parameters, $schemeRelative);
    } catch (RouteNotFoundException|MissingMandatoryParametersException|InvalidParameterException $e) {
      // allow empty url
    }

    return null;
  }

  public function getFunctions(): array {
    return [
        new TwigFunction('url', [$this, 'getUrl'], ['is_safe_callback' => [$this->routingExtension, 'isUrlGenerationSafe']]),
        new TwigFunction('path', [$this, 'getPath'], ['is_safe_callback' => [$this->routingExtension, 'isUrlGenerationSafe']]),
    ];
  }
}
