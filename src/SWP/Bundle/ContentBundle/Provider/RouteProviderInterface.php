<?php

/**
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

namespace SWP\Bundle\ContentBundle\Provider;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;

interface RouteProviderInterface
{
    /**
     * Gets the base route.
     *
     * @return RouteInterface
     */
    public function getBaseRoute();

    /**
     * Gets one route by id.
     *
     * @return RouteInterface|void
     */
    public function getOneById($id);

    /**
     * Gets the route for article. Indicates route the article should have.
     *
     * @param ArticleInterface $article
     *
     * @return RouteInterface|null
     */
    public function getRouteForArticle(ArticleInterface $article);
}
