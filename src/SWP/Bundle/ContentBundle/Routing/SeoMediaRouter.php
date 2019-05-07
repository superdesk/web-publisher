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
use Symfony\Cmf\Component\Routing\VersatileGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SeoMediaRouter extends Router implements VersatileGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $item = $name->getValues()->getImage();

        $parameters['mediaId'] = $item->getAssetId();
        $parameters['extension'] = $item->getFileExtension();

        return parent::generate('swp_seo_media_get', $parameters, $referenceType);
    }

    public function supports($name): bool
    {
        return $name instanceof Meta && $name->getValues() instanceof ArticleSeoMediaInterface;
    }

    public function getRouteDebugMessage($name, array $parameters = array()): string
    {
        return 'Route for media '.$name->getValues()->getId().' not found';
    }
}
