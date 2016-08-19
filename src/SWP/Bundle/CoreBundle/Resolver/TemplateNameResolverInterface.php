<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\CoreBundle\Resolver;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;

interface TemplateNameResolverInterface
{
    /**
     * @param object $object
     * @param string $default
     *
     * @return string
     */
    public function resolve($object, $default = 'article.html.twig');

    /**
     * @param ArticleInterface $article
     * @param string           $default
     *
     * @return string
     */
    public function resolveFromArticle(ArticleInterface $article, $default = 'article.html.twig');

    /**
     * @param RouteInterface $route
     * @param string         $default
     *
     * @return string
     */
    public function resolveFromRoute(RouteInterface $route, $default = 'article.html.twig');
}
