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

namespace SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR;

use Jackalope\Query\SqlQuery;
use PHPCR\Query\QueryInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\StorageBundle\Doctrine\ODM\PHPCR\DocumentRepository;

class ArticleRepository extends DocumentRepository implements ArticleRepositoryInterface
{
    /**
     * @param string $id
     *
     * @return object
     */
    public function findBaseNode($id)
    {
        return $this->dm->find(null, $id);
    }

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

    public function getByCriteria(Criteria $criteria, array $sorting)
    {
        $routeIdentifier = $this->dm->getNodeForDocument($route)->getIdentifier();
        $query = $this->getRouteArticlesQuery($routeIdentifier, $parameters);
        $articles = $this->dm->getDocumentsByPhpcrQuery($query, Article::class);

        //$this->getRouteArticlesQuery($routeIdentifier, [])->execute()->getRows()->count()

        if (isset($parameters['limit'])) {
            $query->setLimit($parameters['limit']);
        }

        if (isset($parameters['start'])) {
            $query->setOffset($parameters['start']);
        }

        return $articles;
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
        $queryStr = sprintf('SELECT S.route FROM [nt:unstructured] as S WHERE S.route="%s" AND S.status="%s"', $identifier, ArticleInterface::STATUS_PUBLISHED);
        if (count($order) === 2) {
            $allowedOrders = ['ASC', 'DESC'];
            if (!in_array(strtoupper($order[1]), $allowedOrders)) {
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
        }

        return $this->dm->createPhpcrQuery($queryStr, QueryInterface::JCR_SQL2);
    }
}
