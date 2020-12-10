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
use FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface;
use FOS\ElasticaBundle\Repository;
use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;

class PackageRepository extends Repository
{
    public function findByCriteria(Criteria $criteria): PaginatorAdapterInterface
    {
        $fields = $criteria->getFilters()->getFields();

        $boolFilter = new BoolQuery();

        if (null !== $criteria->getTerm() && '' !== $criteria->getTerm()) {
            $boolQuery = new BoolQuery();

            $searchBy = ['headline', 'description'];
            $term = $criteria->getTerm();
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

        if (null !== $fields->get('organization') && '' !== $fields->get('organization')) {
            $boolFilter->addFilter(new Term(['organization.id' => $fields->get('organization')]));
        }

        if (null !== $fields->get('sources') && !empty($fields->get('sources'))) {
            $boolFilter->addFilter(new Query\Terms('sources', $fields->get('sources')));
        }

        if ((null !== $fields->get('authors')) && !empty($fields->get('authors'))) {
            $bool = new BoolQuery();
            $bool->addFilter(new Query\Terms('authors.id', $fields->get('authors')));
            $nested = new Nested();
            $nested->setPath('authors');
            $nested->setQuery($bool);
            $boolFilter->addMust($nested);
        }

        if (null !== $fields->get('publishedAfter') || null !== $fields->get('publishedBefore')) {
            $nested = new Nested();
            $nested->setPath('articles');
            $boolQuery = new BoolQuery();
            $boolQuery->addMust(new Range(
                'articles.publishedAt',
                [
                    'gte' => $fields->get('publishedAfter'),
                    'lte' => $fields->get('publishedBefore'),
                ]
            ));

            $boolQuery->addMust(new Term(['articles.isPublishable' => true]));
            $nested->setQuery($boolQuery);
            $boolFilter->addMust($nested);
        }

        if (null !== $fields->get('statuses') && !empty($fields->get('statuses'))) {
            $boolFilter->addFilter(new Query\Terms('status', $fields->get('statuses')));
        }

        $bool = new BoolQuery();
        if (null !== $fields->get('tenants') && !empty($fields->get('tenants'))) {
            $bool->addFilter(new Query\Terms('articles.tenantCode', $fields->get('tenants')));
        }

        if (null !== $fields->get('routes') && !empty($fields->get('routes'))) {
            $bool->addFilter(new Query\Terms('articles.route.id', $fields->get('routes')));
        }

        if (null !== $fields->get('language') && '' !== $fields->get('language')) {
            $boolFilter->addFilter(new Term(['language' => $fields->get('language')]));
        }

        if (!empty($bool->getParams())) {
            $nested = new Nested();
            $nested->setPath('articles');
            $nested->setQuery($bool);
            $boolFilter->addMust($nested);
        }

        $functionScore = new Query\FunctionScore();
        $functionScore->setScoreMode(Query\FunctionScore::SCORE_MODE_SUM);
        $functionScore->setBoostMode(Query\FunctionScore::BOOST_MODE_MULTIPLY);
        $functionScore->addWeightFunction(1);
        $now = new \DateTime();
        $functionScore->addDecayFunction(
            Query\FunctionScore::DECAY_GAUSS,
            'publishedAt',
            $now->format('Y-m-d'),
            '31d',
            '1d',
            0.5,
            5
        );

        $functionScore->addDecayFunction(
            Query\FunctionScore::DECAY_GAUSS,
            'publishedAt',
            $now->format('Y-m-d'),
            '365d',
            '1d',
            0.5,
            2
        );

        $functionScore->setQuery($boolFilter);

        $query = Query::create($functionScore)
            ->addSort([
                $criteria->getOrder()->getField() => $criteria->getOrder()->getDirection(),
            ]);

        return $this->createPaginatorAdapter($query);
    }
}
