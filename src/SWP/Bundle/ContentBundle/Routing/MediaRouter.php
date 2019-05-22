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
use Symfony\Cmf\Component\Routing\VersatileGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

class MediaRouter extends Router implements VersatileGeneratorInterface
{
    private $mediaManager;

    public function __construct(
        ContainerInterface $container,
        $resource,
        array $options = [],
        RequestContext $context = null,
        ContainerInterface $parameters = null,
        LoggerInterface $logger = null,
        string $defaultLocale = null
    ) {
        $this->mediaManager = $container->get('swp_content_bundle.manager.media');

        parent::__construct($container, $resource, $options, $context, $parameters, $logger, $defaultLocale);
    }

    public function getRouteDebugMessage($meta, array $parameters = array()): string
    {
        return 'Route for media '.$meta->getValues()->getId().' not found';
    }

    public function supports($meta): bool
    {
        return $meta instanceof Meta && (
            $meta->getValues() instanceof ArticleMediaInterface ||
            $meta->getValues() instanceof ImageRenditionInterface
        );
    }

    /** @param Meta $meta */
    public function generate($meta, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        $item = $this->getItem($meta);
        if ($meta->getValues() instanceof ImageRenditionInterface && null !== ($previewUrl = $meta->getValues()->getPreviewUrl())) {
            return $previewUrl;
        }

        if ($item instanceof PreviewUrlAwareInterface && null !== ($previewUrl = $item->getPreviewUrl())) {
            return $previewUrl;
        }

        if (array_key_exists('webp', $parameters) && $parameters['webp']) {
            return str_replace('.'.$item->getFileExtension(), '.webp', $this->mediaManager->getMediaPublicUrl($item));
        }

        return  $this->mediaManager->getMediaPublicUrl($item);
    }

    private function getItem($meta): ?FileInterface
    {
        if (!$meta instanceof Meta) {
            return null;
        }

        if (($rendition = $meta->getValues()) instanceof ImageRendition) {
            return $rendition->getImage();
        }

        if (($image = $meta->getValues()->getImage()) instanceof ImageInterface) {
            return $image;
        }

        if (($file = $meta->getValues()->getFile()) instanceof FileInterface) {
            return $file;
        }
    }
}
