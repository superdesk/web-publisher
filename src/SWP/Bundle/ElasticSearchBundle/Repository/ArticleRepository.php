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
use Elastica\QueryBuilder\DSL\Suggest;
use FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface;
use FOS\ElasticaBundle\Repository;
use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;
use SWP\Bundle\ElasticSearchBundle\Loader\SearchResultLoader;

class ArticleRepository extends Repository
{
    public function findByCriteria(Criteria $criteria, array $extraFields = [], bool $searchByBody = false): PaginatorAdapterInterface
    {
        $fields = $criteria->getFilters()->getFields();
        $boolFilter = new BoolQuery();

        $term = $criteria->getTerm();
        if (null !== $term && '' !== $term) {
            $searchBy = ['title^5', 'lead^4', 'body^3', 'keywords.name^2'];

            foreach ($extraFields as $extraField) {
                $searchBy[] = 'extra.'.$extraField;
            }

            if ($searchByBody) {
                array_splice($searchBy, 2, 0, ['body']);
            }

            $boolQuery = new BoolQuery();

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
            $multiMatchQuery->setTieBreaker(0.3);

            $boolQuery->addShould($multiMatchQuery);

            $bool = new BoolQuery();
            $phraseMultiMatchQuery3 = new MultiMatch();
            $phraseMultiMatchQuery3->setQuery($term);
            $phraseMultiMatchQuery3->setFields(['authors.name', 'authors.biography']);
            $phraseMultiMatchQuery3->setType(MultiMatch::TYPE_PHRASE);
            $phraseMultiMatchQuery3->setParam('boost', 4);
            $bool->addShould($phraseMultiMatchQuery3);

            $multiMatchQuery4 = new MultiMatch();
            $multiMatchQuery4->setQuery($term);
            $multiMatchQuery4->setFields(['authors.name', 'authors.biography']);
            $multiMatchQuery4->setOperator(MultiMatch::OPERATOR_AND);
            $multiMatchQuery4->setParam('boost', 2);
            $multiMatchQuery4->setFuzziness(0);
            $bool->addShould($multiMatchQuery4);

            $phraseMultiMatchQuery5 = new MultiMatch();
            $phraseMultiMatchQuery5->setQuery($term);
            $phraseMultiMatchQuery5->setFields(['authors.name', 'authors.biography']);
            $phraseMultiMatchQuery5->setFuzziness(1);

            $nested = new Nested();
            $nested->setPath('authors');
            $functionScore = new Query\FunctionScore();
            $functionScore->addWeightFunction(15, new Query\Match('authors.name', $term));
            $functionScore->addWeightFunction(5, new Query\Match('authors.biography', $term));
            $functionScore->addWeightFunction(15, new Query\MatchPhrase('authors.name', $term));
            $functionScore->addWeightFunction(10, new Query\MatchPhrase('authors.biography', $term));
            $functionScore->setQuery($bool);
            $nested->setQuery($functionScore);

            $boolQuery->addShould($nested);

            $boolFilter->addMust($boolQuery);
        } else {
            $boolFilter->addMust(new MatchAll());
        }

        if (null !== $fields->get('keywords') && !empty($fields->get('keywords'))) {
            $bool = new BoolQuery();
            $bool->addFilter(new Query\Terms('keywords.name', $fields->get('keywords')));
            $nested = new Nested();
            $nested->setPath('keywords');
            $nested->setQuery($bool);
            $boolFilter->addMust($nested);
        }

        if (null !== $fields->get('authors') && !empty($fields->get('authors'))) {
            $bool = new BoolQuery();
            foreach ($fields->get('authors') as $author) {
                $bool->addFilter(new Query\Match('authors.name', $author));
                $bool->addFilter(new Query\Match('authors.biography', $author));
            }

            $nested = new Nested();
            $nested->setPath('authors');
            $nested->setQuery($bool);
            $boolFilter->addMust($nested);
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
            $boolFilter->addFilter(new Range(
                'publishedAt',
                [
                    'gte' => null !== $fields->get('publishedAfter') ? $fields->get('publishedAfter')->format('Y-m-d') : null,
                    'lte' => null !== $fields->get('publishedBefore') ? $fields->get('publishedBefore')->format('Y-m-d') : null,
                ]
            ));

            $boolFilter->addFilter(new Term(['isPublishable' => true]));
        }

        if (!empty($bool->getParams())) {
            $boolFilter->addMust($bool);
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
                '_score' => 'desc',
                $criteria->getOrder()->getField() => $criteria->getOrder()->getDirection(),
            ]);

        $query->setSize(SearchResultLoader::MAX_RESULTS);
        $query->setTrackScores(true);

        return $this->createPaginatorAdapter($query);
    }

    public function getSuggestedTerm(string $term): string
    {
        $suggestQuery = new Suggest();
        $suggestQuery->phrase('our_suggestion', '_all');

        $phraseMultiMatchQuery = new MultiMatch();
        $phraseMultiMatchQuery->setQuery($term);
        $phraseMultiMatchQuery->setFields('_all');
        $phraseMultiMatchQuery->setType(MultiMatch::TYPE_PHRASE);
        $phraseMultiMatchQuery->setParam('boost', 50);

        $query = new \Elastica\Query($phraseMultiMatchQuery);
        $suggest = new \Elastica\Suggest();
        $suggest->setParam(
            'phrase',
            [
                'text' => $term,
                'phrase' => ['field' => '_all'],
            ]
        );

        $query->setSuggest($suggest);

        $adapter = $this->createPaginatorAdapter($query);
        $suggest = $adapter->getSuggests();

        return $suggest['phrase'][0]['options'][0]['text'] ?? '';
    }
}
