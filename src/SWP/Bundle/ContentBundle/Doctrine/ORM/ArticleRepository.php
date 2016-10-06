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

namespace SWP\Bundle\ContentBundle\Doctrine\ORM;

use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;

class ArticleRepository extends EntityRepository implements ArticleRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneBySlug($slug)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function findAllArticles()
    {
        throw new \Exception('Not implemented');
    }

    /**
     * @param string $identifier
     * @param array  $order
     *
     * @return SqlQuery
     *
     * @throws \Exception
     */
    public function getQueryForRouteArticles(string $identifier, array $order = []) : SqlQuery
    {
        throw new \Exception('Not implemented');
    }
}
