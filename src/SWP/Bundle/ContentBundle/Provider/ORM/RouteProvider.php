<?php

/*
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

namespace SWP\Bundle\ContentBundle\Provider\ORM;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

class RouteProvider implements RouteProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $routeRepository;

    /**
     * RouteProvider constructor.
     *
     * @param RepositoryInterface $routeRepository Route repository
     */
    public function __construct(
        RepositoryInterface $routeRepository
    ) {
        $this->routeRepository = $routeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseRoute()
    {
        throw new \Exception('Not implemented in ORM');
    }

    /**
     * {@inheritdoc}
     */
    public function getOneById($id)
    {
        return $this->routeRepository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteForArticle(ArticleInterface $article)
    {
        return $article->getRoute();
    }
}
