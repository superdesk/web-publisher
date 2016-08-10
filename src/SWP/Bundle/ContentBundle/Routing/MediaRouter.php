<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Routing;

use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Cmf\Component\Routing\VersatileGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MediaRouter extends Router implements VersatileGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $item = null;
        if ($name->getValues()->getImage() instanceof ImageInterface) {
            $item = $name->getValues()->getImage();
        } elseif ($name->getValues()->getFile() instanceof FileInterface) {
            $item = $name->getValues()->getFile();
        }

        if (null === $item) {
            return;
        }

        $parameters['mediaId'] = $item->getId();
        $parameters['extension'] = $item->getFileExtension();

        return parent::generate('swp_media_get', $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return $name instanceof Meta && $name->getValues() instanceof ArticleMedia;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteDebugMessage($name, array $parameters = array())
    {
        return "Route for media ".$name->getValues()->getId()." not found";
    }
}