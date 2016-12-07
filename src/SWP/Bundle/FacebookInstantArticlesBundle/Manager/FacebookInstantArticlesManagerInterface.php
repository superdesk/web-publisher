<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Facebook Instant Articles Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FacebookInstantArticlesBundle\Manager;

use Facebook;

interface FacebookInstantArticlesManagerInterface
{
    /**
     * @param Facebook\Facebook $facebook
     * @param string            $pageId
     *
     * @return null|string
     */
    public function getPageAccessToken(Facebook\Facebook $facebook, $pageId);

    /**
     * @param Facebook\Facebook $facebook
     *
     * @return Facebook\GraphNodes\GraphEdge|null
     */
    public function getPagesAndTokens(Facebook\Facebook $facebook);
}
