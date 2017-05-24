<?php

declare(strict_types=1);

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

namespace SWP\Bundle\ContentBundle\Doctrine;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Storage\Repository\RepositoryInterface;

interface ArticleRepositoryInterface extends RepositoryInterface
{
    /**
     * Finds one article by slug.
     *
     * @param string $slug
     *
     * @return ArticleInterface
     */
    public function findOneBySlug($slug);

    /**
     * Finds all articles.
     *
     * @return mixed
     */
    public function findAllArticles();

    /**
     * @param string $identifier
     * @param array  $order
     *
     * @return object
     */
    public function getQueryForRouteArticles(string $identifier, array $order);

    /**
     * @param Criteria $criteria
     * @param array    $sorting
     *
     * @return mixed
     */
    public function getByCriteria(Criteria $criteria, array $sorting);

    /**
     * @param Criteria $criteria
     * @param string   $status
     *
     * @return int
     */
    public function countByCriteria(Criteria $criteria, $status = ArticleInterface::STATUS_PUBLISHED): int;

    /**
     * @param Criteria $criteria
     * @param array    $sorting
     *
     * @return array
     */
    public function findArticlesByCriteria(Criteria $criteria, array $sorting = []): array;
}
