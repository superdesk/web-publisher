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

use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractMediaController
{
    /**
     * @Route("/media/{mediaId}.{extension}", methods={"GET"}, options={"expose"=true}, requirements={"mediaId"=".+"}, name="swp_media_get")
     */
    public function getAction(string $mediaId, string $extension)
    {
        return $this->getMedia($mediaId, $extension);
    }
}
