<?php

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
     * Gets the route for article. Indicates route the article should have.
     * 
     * @param ArticleInterface $article
     *
     * @return RouteInterface
     */
    public function getRouteForArticle(ArticleInterface $article);
}
