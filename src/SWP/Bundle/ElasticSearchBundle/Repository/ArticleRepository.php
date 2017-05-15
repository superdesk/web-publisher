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
use Elastica\Query\Range;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Repository;
use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;

class ArticleRepository extends Repository
{
    public function findByCriteria(Criteria $criteria)
    {
        $fields = $criteria->getFiltering()->getFields();
        $boolFilter = new BoolQuery();

        if ($criteria->getTerm() !== null && $criteria->getTerm() !== '') {
            $query = new MultiMatch();
            $query->setFields(['title^3', 'lead^2', 'body^1']);
            $query->setQuery($criteria->getTerm());
            $boolFilter->addMust($query);
        } else {
            $boolFilter->addMust(new MatchAll());
        }

        if ($fields['route'] !== null && $fields['route'] !== '') {
            $boolFilter->addMust(new Term(['route.name' => $fields['route']]));
        }

        if ($fields['tenantCode'] !== null && $fields['tenantCode'] !== '') {
            $boolFilter->addMust(new Term(['tenantCode' => $fields['tenantCode']]));
        }

        if ($fields['source'] !== null && $fields['source'] !== '') {
            $boolFilter->addMust(new Term(['source' => $fields['source']]));
        }

        if ($fields['authors'] !== null && !empty($fields['authors'])) {
            foreach ($fields['authors'] as $author) {
                $boolFilter->addShould(new Query\Match(['metadata' => $author]));
            }
        }

        if (null !== $fields['publishedAfter'] || null !== $fields['publishedBefore']) {
            $boolFilter->addMust(new Range(
                'publishedAt',
                [
                    'gte' => $fields['publishedAfter'],
                    'lte' => $fields['publishedBefore'],
                ]
            ));

            $boolFilter->addMust(new \Elastica\Query\Term(['isPublishable' => true]));
        }

        $query = Query::create($boolFilter)
            ->addSort([
                $criteria->getOrdering()->getField() => $criteria->getOrdering()->getDirection(),
            ]);

        return $this->createPaginatorAdapter($query);
    }
}
