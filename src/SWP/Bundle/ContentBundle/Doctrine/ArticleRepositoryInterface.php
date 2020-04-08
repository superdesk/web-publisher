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

use Doctrine\ORM\QueryBuilder;
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

    public function findAllArticles();

    public function getQueryForRouteArticles(string $identifier, array $order);

    public function getByCriteria(Criteria $criteria, array $sorting): QueryBuilder;

    public function countByCriteria(Criteria $criteria, $status = ArticleInterface::STATUS_PUBLISHED): int;

    public function getArticlesByCriteria(Criteria $criteria, array $sorting = []): QueryBuilder;

    public function getArticlesByCriteriaIds(Criteria $criteria): QueryBuilder;

    public function getArticlesByBodyContent(string $content): array;
}
