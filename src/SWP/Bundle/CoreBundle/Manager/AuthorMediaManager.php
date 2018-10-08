<?php

declare(strict_types=1);

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
use Symfony\Component\Routing\RouterInterface;

final class AuthorMediaManager extends BaseMediaManager
{
    public function getMediaPublicUrl(FileInterface $media): string
    {
        $tenant = $this->tenantContext->getTenant();
        if ($subdomain = $tenant->getSubdomain()) {
            $context = $this->router->getContext();
            $context->setHost($subdomain.'.'.$context->getHost());
        }

        return $this->getMediaUri($media);
    }

    public function getMediaUri(FileInterface $media, $type = RouterInterface::ABSOLUTE_PATH): string
    {
        return $this->router->generate('swp_author_media_get', [
            'mediaId' => $media->getAssetId(),
            'extension' => $media->getFileExtension(),
        ], $type);
    }

    protected function getMediaBasePath(): string
    {
        $tenant = $this->tenantContext->getTenant();
        $pathElements = ['swp', $tenant->getOrganization()->getCode(), 'authors'];

        return implode('/', $pathElements);
    }
}
