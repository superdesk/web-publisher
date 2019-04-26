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

namespace SWP\Bundle\ContentBundle\Controller;

use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class SeoMediaController extends AbstractMediaController
{
    /**
     * @Route("/author/media/{mediaId}.{extension}", methods={"GET"}, options={"expose"=true}, requirements={"mediaId"=".+"}, name="swp_author_media_get")
     */
    public function getAction(string $mediaId, string $extension): Response
    {
        return $this->getMedia($mediaId, $extension);
    }

    public function getMediaManager(): MediaManagerInterface
    {
        return $this->get('swp_core_bundle.manager.author_media');
    }
}
