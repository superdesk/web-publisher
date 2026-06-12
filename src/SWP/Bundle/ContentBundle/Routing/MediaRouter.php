<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Routing;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Bundle\ContentBundle\Model\ImageRendition;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Bundle\ContentBundle\Model\PreviewUrlAwareInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Cmf\Component\Routing\VersatileGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

class MediaRouter extends Router implements VersatileGeneratorInterface {
  private $mediaManager;

  const OBJECT_BASED_ROUTE_NAME = "__media_router_route_name__";

  public function __construct(
      ContainerInterface $container,
                         $resource,
      array              $options = [],
      RequestContext     $context = null,
      ContainerInterface $parameters = null,
      LoggerInterface    $logger = null,
      string             $defaultLocale = null
  ) {
    $this->mediaManager = $container->get('swp_content_bundle.manager.media');

    parent::__construct($container, $resource, $options, $context, $parameters, $logger, $defaultLocale);
  }

  public static function getSubscribedServices(): array {
    return array_merge(parent::getSubscribedServices(), [
        'swp_content_bundle.manager.media' => \SWP\Bundle\ContentBundle\Manager\MediaManagerInterface::class,
    ]);
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

  public function generate(string $name, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string {
    $meta = null;
    if (self::OBJECT_BASED_ROUTE_NAME === $name && array_key_exists(RouteObjectInterface::ROUTE_OBJECT, $parameters)) {
      $meta = $parameters[RouteObjectInterface::ROUTE_OBJECT];
      unset($parameters[RouteObjectInterface::ROUTE_OBJECT]);
    }

    if (!$meta instanceof Meta) {
      throw new RouteNotFoundException(sprintf('Route "%s" is not supported by this router.', $name));
    }

    $item = $this->getItem($meta);
    if (null === $item) {
      throw new RouteNotFoundException('No media item found for the given route object.');
    }

    if ($meta->getValues() instanceof ImageRenditionInterface && null !== ($previewUrl = $meta->getValues()->getPreviewUrl())) {
      return $previewUrl;
    }

    if ($item instanceof PreviewUrlAwareInterface && null !== ($previewUrl = $item->getPreviewUrl())) {
      return $previewUrl;
    }

    return $this->getUrlWithCorrectExtension($item, $parameters);
  }

  private function getItem(Meta $meta): ?FileInterface {
    if (($rendition = $meta->getValues()) instanceof ImageRendition) {
      return $rendition->getImage();
    }

    if (($image = $meta->getValues()->getImage()) instanceof ImageInterface) {
      return $image;
    }

    if (($file = $meta->getValues()->getFile()) instanceof FileInterface) {
      return $file;
    }

    return null;
  }

  private function getUrlWithCorrectExtension(FileInterface $item, array $parameters): string {
    $url = $this->mediaManager->getMediaPublicUrl($item);

    if (
        $item instanceof ImageInterface &&
        array_key_exists('webp', $parameters) &&
        true === $parameters['webp'] &&
        $item->hasVariant(ImageInterface::VARIANT_WEBP)
    ) {
      return str_replace('.' . $item->getFileExtension(), '.webp', $url);
    }

    return $url;
  }
}
