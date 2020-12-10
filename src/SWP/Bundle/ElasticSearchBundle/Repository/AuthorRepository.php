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
use FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface;
use FOS\ElasticaBundle\Repository;
use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;

class AuthorRepository extends Repository
{
    public function findByCriteria(Criteria $criteria): PaginatorAdapterInterface
    {
        $boolFilter = new BoolQuery();

        if (null !== $criteria->getTerm() && '' !== $criteria->getTerm()) {
            $boolQuery = new BoolQuery();
            $term = $criteria->getTerm();

            $searchBy = ['name', 'slug'];
            $phraseMultiMatchQuery = new MultiMatch();
            $phraseMultiMatchQuery->setQuery($term);
            $phraseMultiMatchQuery->setFields($searchBy);
            $phraseMultiMatchQuery->setType(MultiMatch::TYPE_PHRASE);
            $phraseMultiMatchQuery->setParam('boost', 4);

            $boolQuery->addShould($phraseMultiMatchQuery);

            $phraseMultiMatchQuery2 = new MultiMatch();
            $phraseMultiMatchQuery2->setQuery($term);
            $phraseMultiMatchQuery2->setFields($searchBy);
            $phraseMultiMatchQuery2->setFuzziness(1);

            $boolQuery->addShould($phraseMultiMatchQuery);

            $multiMatchQuery = new MultiMatch();
            $multiMatchQuery->setQuery($term);
            $multiMatchQuery->setFields($searchBy);
            $multiMatchQuery->setOperator(MultiMatch::OPERATOR_AND);
            $multiMatchQuery->setParam('boost', 2);
            $multiMatchQuery->setFuzziness(0);

            $boolQuery->addShould($multiMatchQuery);
            $boolFilter->addMust($boolQuery);
        } else {
            $boolFilter->addMust(new MatchAll());
        }

        $query = Query::create($boolFilter)
            ->addSort([
                '_score' => 'desc',
                $criteria->getOrder()->getField() => $criteria->getOrder()->getDirection(),
            ]);

        return $this->createPaginatorAdapter($query);
    }
}
