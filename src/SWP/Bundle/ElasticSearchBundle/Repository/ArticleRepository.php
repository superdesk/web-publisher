<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher ElasticSearch Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ElasticSearchBundle\Repository;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchAll;
use Elastica\Query\MultiMatch;
use Elastica\Query\Nested;
use Elastica\Query\Range;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Repository;
use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;

class ArticleRepository extends Repository
{
    /**
     * @param Criteria $criteria
     *
     * @return \FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface
     */
    public function findByCriteria(Criteria $criteria)
    {
        $fields = $criteria->getFilters()->getFields();
        $boolFilter = new BoolQuery();

        if (null !== $criteria->getTerm() && '' !== $criteria->getTerm()) {
            $query = new MultiMatch();
            $query->setFields(['title^3', 'lead^2', 'body^1']);
            $query->setQuery($criteria->getTerm());
            $boolFilter->addMust($query);
        } else {
            $boolFilter->addMust(new MatchAll());
        }

        if (null !== $fields->get('authors') && !empty($fields->get('authors'))) {
            foreach ($fields->get('authors') as $author) {
                $boolFilter->addFilter(new Query\Match('author', $author));
            }
        }

        if (null !== $fields->get('sources') && !empty($fields->get('sources'))) {
            $nested = new Nested();
            $nested->setPath('sources');
            $boolQuery = new BoolQuery();
            $boolQuery->addMust(new Query\Terms('sources.name', $fields->get('sources')));
            $nested->setQuery($boolQuery);
            $boolFilter->addMust($nested);
        }

        if (null !== $fields->get('statuses') && !empty($fields->get('statuses'))) {
            $boolFilter->addFilter(new Query\Terms('status', $fields->get('statuses')));
        }

        if (null !== $fields->get('metadata') && !empty($fields->get('metadata'))) {
            foreach ($fields->get('metadata') as $key => $values) {
                foreach ((array) $values as $value) {
                    $boolFilter->addFilter(new Query\Match($key, $value));
                }
            }
        }

        if (null !== $fields->get('tenantCode')) {
            $boolFilter->addFilter(new Term(['tenantCode' => $fields->get('tenantCode')]));
        }

        $bool = new BoolQuery();
        if (null !== $fields->get('routes') && !empty($fields->get('routes'))) {
            $bool->addFilter(new Query\Terms('route.id', $fields->get('routes')));
        }

        if (null !== $fields->get('publishedAfter') || null !== $fields->get('publishedBefore')) {
            $range = new Range(
                'publishedAt',
                [
                    'gte' => null !== $fields->get('publishedAfter') ? $fields->get('publishedAfter')->format('Y-m-d') : null,
                    'lte' => null !== $fields->get('publishedBefore') ? $fields->get('publishedBefore')->format('Y-m-d') : null,
                ]
            );

            $boolFilter->addFilter($range);
            $boolFilter->addFilter(new \Elastica\Query\Term(['isPublishable' => true]));
        }

        if (!empty($bool->getParams())) {
            $boolFilter->addMust($bool);
        }

        $query = Query::create($boolFilter)
            ->addSort([
                $criteria->getOrder()->getField() => $criteria->getOrder()->getDirection(),
            ]);

        return $this->createPaginatorAdapter($query);
    }
}
