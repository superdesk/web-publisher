<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Manager;

use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\CoreBundle\Manager\MediaManager as BaseMediaManager;

class AuthorMediaManager extends BaseMediaManager
{
    /**
     * {@inheritdoc}
     */
    public function getMediaUri(FileInterface $media, $type = RouterInterface::ABSOLUTE_PATH)
    {
        return $this->router->generate('swp_author_media_get', [
            'mediaId' => $media->getAssetId(),
            'extension' => $media->getFileExtension(),
        ], $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaPublicUrl(FileInterface $media)
    {
        $tenant = $this->tenantContext->getTenant();
        if ($subdomain = $tenant->getSubdomain()) {
            $context = $this->router->getContext();
            $context->setHost($subdomain.'.'.$context->getHost());
        }

        return parent::getMediaPublicUrl($media);
    }

    /**
     * @return string
     */
    protected function getMediaBasePath(): string
    {
        $tenant = $this->tenantContext->getTenant();
        $pathElements = ['swp', $tenant->getOrganization()->getCode(), 'authors'];

        return implode('/', $pathElements);
    }
}
