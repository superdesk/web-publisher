<?php

declare(strict_types=1);

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

namespace SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR;

use Jackalope\Query\SqlQuery;
use PHPCR\Query\QueryInterface;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\StorageBundle\Doctrine\ODM\PHPCR\DocumentRepository;

class ArticleRepository extends DocumentRepository implements ArticleRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneBySlug($slug)
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    /**
     * {@inheritdoc}
     */
    public function findAllArticles()
    {
        return $this->createQueryBuilder('o')->getQuery();
    }

    /**
     * @param string $identifier
     * @param array  $order
     *
     * @return SqlQuery
     *
     * @throws \Exception
     */
    public function getQueryForRouteArticles(string $identifier, array $order) : SqlQuery
    {
        $queryStr = sprintf("SELECT * FROM nt:unstructured as S WHERE S.phpcr:class='%s' AND S.route=%s AND S.status=published", Article::class, $identifier);
        $allowedOrders = ['ASC', 'DESC'];
        if (count($order) !== 2 || !in_array(strtoupper($order[1]), $allowedOrders)) {
            throw new \Exception('Order filter must have two parameters with second one asc or desc, e.g. order(id, desc)');
        }

        if ($order[0] === 'id') {
            $order[0] = 'jcr:uuid';
        } else {
            // Check that the given parameter is actually a field name of a route
            $metaData = $this->dm->getClassMetadata('SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article');
            if (!in_array($order[0], $metaData->getFieldNames())) {
                throw new \Exception(sprintf('Only those parameters are allowed: %s. %s was given ', implode(', ', $metaData->getFieldNames()), $order[0]));
            }
        }
        $queryStr .= sprintf(' ORDER BY S.%s %s', $order[0], $order[1]);

        return $this->dm->createPhpcrQuery($queryStr, QueryInterface::JCR_SQL2);
    }
}
