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

use Doctrine\ORM\QueryBuilder;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleAuthorInterface;
use SWP\Bundle\ContentBundle\Model\ArticleAuthorReference;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleSourceReference;
use SWP\Bundle\ContentBundle\Model\Metadata;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

/**
 * Class ArticleRepository.
 */
class ArticleRepository extends EntityRepository implements ArticleRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneBySlug($slug)
    {
        return $this->findOneBy([
            'slug' => $slug,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findAllArticles()
    {
        return $this->getQueryByCriteria(new Criteria(), [], 'a')->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getByCriteria(Criteria $criteria, array $sorting): QueryBuilder
    {
        $qb = $this->getQueryByCriteria($criteria, $sorting, 'a');
        $qb->andWhere('a.status = :status')
            ->setParameter('status', $criteria->get('status', ArticleInterface::STATUS_PUBLISHED))
            ->leftJoin('a.media', 'm')
            ->leftJoin('m.renditions', 'r')
            ->addSelect('m', 'r');

        $this->applyCustomFiltering($qb, $criteria);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function countByCriteria(Criteria $criteria, $status = ArticleInterface::STATUS_PUBLISHED): int
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)');

        if (null !== $status) {
            $queryBuilder
                ->where('a.status = :status')
                ->setParameter('status', $criteria->get('status', $status));
        }

        $this->applyCustomFiltering($queryBuilder, $criteria);
        $this->applyCriteria($queryBuilder, $criteria, 'a');

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getArticlesByCriteria(Criteria $criteria, array $sorting = []): QueryBuilder
    {
        $queryBuilder = $this->getArticlesByCriteriaIds($criteria);
        $queryBuilder->andWhere('a.route IS NOT NULL');
        $this->applyCustomFiltering($queryBuilder, $criteria);
        $this->applyCriteria($queryBuilder, $criteria, 'a');
        $this->applySorting($queryBuilder, $sorting, 'a', $criteria);
        $articlesQueryBuilder = clone $queryBuilder;
        $this->applyLimiting($queryBuilder, $criteria);
        $selectedArticles = $queryBuilder->getQuery()->getScalarResult();
        if (!is_array($selectedArticles)) {
            return [];
        }

        $ids = [];

        foreach ($selectedArticles as $partialArticle) {
            $ids[] = $partialArticle['a_id'];
        }
        $articlesQueryBuilder->addSelect('a')
            ->leftJoin('a.media', 'm')
            ->leftJoin('m.renditions', 'r')
            ->addSelect('m', 'r')
            ->andWhere('a.id IN (:ids)')
            ->setParameter('ids', $ids);

        return $articlesQueryBuilder;
    }

    public function getArticlesByCriteriaIds(Criteria $criteria): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->select('partial a.{id}')
            ->where('a.status = :status')
            ->setParameter('status', $criteria->get('status', ArticleInterface::STATUS_PUBLISHED));

        return $queryBuilder;
    }

    public function getArticlesByBodyContent(string $content): array
    {
        $queryBuilder = $this->createQueryBuilder('a');
        $like = $queryBuilder->expr()->like('a.body', $queryBuilder->expr()->literal('%'.$content.'%'));
        $queryBuilder->andWhere($like);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginatedByCriteria(Criteria $criteria, array $sorting = [], PaginationData $paginationData = null)
    {
        $queryBuilder = $this->getQueryByCriteria($criteria, $sorting, 'a');
        $this->applyCustomFiltering($queryBuilder, $criteria);

        if (null === $paginationData) {
            $paginationData = new PaginationData();
        }

        return $this->getPaginator($queryBuilder, $paginationData);
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryForRouteArticles(string $identifier, array $order = [])
    {
        throw new \Exception('Not implemented');
    }

    public function getAuthorWithMostArticles(Criteria $criteria, array $sorting, string $alias): QueryBuilder
    {

//        $query = $this->_em->createQuery('SELECT count(a) as count, aa.id FROM SWP\Bundle\CoreBundle\Model\Article a
//            left join a.authors aa where aa.slug = :slug GROUP BY aa.id order by count desc')
//
//
//            ->setParameter('slug', $parameters['slug'])
//            ->setMaxResults(1)
//            ->getOneOrNullResult();
//
//        //$author = $this->entityManager->getReference(ArticleAuthorInterface::class, $query['id']);

        $queryBuilder = $this->createQueryBuilder($alias)
            ->select('count('.$alias.') as count')
            ->addSelect($alias)
            ->leftJoin('a.authors', 'aa');

        $this->applyCriteria($queryBuilder, $criteria, $alias);
        $queryBuilder->groupBy('aa.id');
        $this->applySorting($queryBuilder, $sorting, $alias, $criteria);
        $this->applyLimiting($queryBuilder, $criteria);

        return $queryBuilder;
    }

    private function applyCustomFiltering(QueryBuilder $queryBuilder, Criteria $criteria)
    {
        $orX = $queryBuilder->expr()->orX();
        if ($criteria->has('extra')) {
            foreach ((array) $criteria->get('extra') as $key => $value) {
                $valueExpression = $queryBuilder->expr()->literal('%'.\rtrim(\ltrim(\serialize([$key => $value]), 'a:1:{'), ';}').'%');
                $orX->add($queryBuilder->expr()->like('a.extra', $valueExpression));
            }

            $queryBuilder->andWhere($orX);
            $criteria->remove('extra');
        }

        if ($criteria->has('metadata')) {
            $queryBuilder
                ->leftJoin('a.data', 'd')
                ->leftJoin('d.services', 's')
                ->leftJoin('d.subjects', 'sb');

            foreach ((array) $criteria->get('metadata') as $key => $value) {
                switch ($key) {
                    case Metadata::SERVICE_KEY:
                        foreach ($value as $service) {
                            $orX->add($queryBuilder->expr()->eq('s.code', $queryBuilder->expr()->literal($service['code'])));
                        }

                        break;
                    case Metadata::SUBJECT_KEY:
                        $andX = $queryBuilder->expr()->andX();
                        foreach ($value as $subject) {
                            $andX->add($queryBuilder->expr()->eq('sb.code', $queryBuilder->expr()->literal($subject['code'])));
                            $andX->add($queryBuilder->expr()->eq('sb.scheme', $queryBuilder->expr()->literal($subject['scheme'])));
                        }

                        $orX->add($andX);

                        break;
                    default:
                        $orX->add($queryBuilder->expr()->eq("d.$key", $queryBuilder->expr()->literal($value)));
                }
            }

            $queryBuilder->andWhere($orX);
            $criteria->remove('metadata');
        }

        if ($criteria->has('keywords')) {
            $queryBuilder->leftJoin('a.keywords', 'k');
            $orX = $queryBuilder->expr()->orX();
            foreach ($criteria->get('keywords') as $key => $value) {
                $queryBuilder->setParameter($key, $value);
                $orX->add($queryBuilder->expr()->eq('k.name', '?'.$key));
                $orX->add($queryBuilder->expr()->eq('k.slug', '?'.$key));
            }

            $queryBuilder->andWhere($orX);
            $criteria->remove('keywords');
        }

        if ($criteria->has('publishedBefore') && null !== $criteria->get('publishedBefore')) {
            $publishedBefore = $criteria->get('publishedBefore');
            $queryBuilder->andWhere('a.publishedAt < :before')
                ->setParameter('before', $publishedBefore instanceof \DateTimeInterface ? $publishedBefore : new \DateTime($publishedBefore));
            $criteria->remove('publishedBefore');
        }

        if ($criteria->has('publishedAfter') && null !== $criteria->get('publishedAfter')) {
            $publishedAfter = $criteria->get('publishedAfter');
            $queryBuilder->andWhere('a.publishedAt > :after')
                ->setParameter('after', $publishedAfter instanceof \DateTimeInterface ? $publishedAfter : new \DateTime($publishedAfter));
            $criteria->remove('publishedAfter');
        }

        if ($criteria->has('query') && strlen($query = trim($criteria->get('query'))) > 0) {
            $like = $queryBuilder->expr()->like('a.title', $queryBuilder->expr()->literal('%'.$query.'%'));

            $queryBuilder->andWhere($like);
            $criteria->remove('query');
        }

        if ($criteria->has('exclude_source') && !empty($criteria->get('exclude_source'))) {
            $articleSourcesQueryBuilder = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('excluded_article.id')
                ->from(ArticleSourceReference::class, 'excluded_asr')
                ->join('excluded_asr.article', 'excluded_article')
                ->join('excluded_asr.articleSource', 'excluded_articleSource');

            $orX = $queryBuilder->expr()->orX();
            foreach ((array) $criteria->get('exclude_source') as $value) {
                $orX->add($articleSourcesQueryBuilder->expr()->eq('excluded_articleSource.name', $articleSourcesQueryBuilder->expr()->literal($value)));
            }
            $articleSourcesQueryBuilder->andWhere($orX);
            $queryBuilder->andWhere($queryBuilder->expr()->notIn('a.id', $articleSourcesQueryBuilder->getQuery()->getDQL()));

            $criteria->remove('exclude_source');
        }

        if ($criteria->has('source') && !empty($criteria->get('source'))) {
            $articleSourcesQueryBuilder = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('article.id')
                ->from(ArticleSourceReference::class, 'asr')
                ->join('asr.article', 'article')
                ->join('asr.articleSource', 'articleSource');
            $orX = $queryBuilder->expr()->orX();
            foreach ((array) $criteria->get('source') as $value) {
                $orX->add($articleSourcesQueryBuilder->expr()->eq('articleSource.name', $articleSourcesQueryBuilder->expr()->literal($value)));
            }
            $articleSourcesQueryBuilder->andWhere($orX);
            $queryBuilder->andWhere($queryBuilder->expr()->in('a.id', $articleSourcesQueryBuilder->getQuery()->getDQL()));

            $criteria->remove('source');
        }

        if (
            ($criteria->has('author') && !empty($criteria->get('author'))) ||
            ($criteria->has('exclude_author') && !empty($criteria->get('exclude_author'))) ||
            ($criteria->has('authorId') && !empty($criteria->get('authorId')))
        ) {
            $queryBuilder->leftJoin('a.authors', 'au');
        }

        if ($criteria->has('author') && !empty($criteria->get('author'))) {
            $orX = $queryBuilder->expr()->orX();
            foreach ((array) $criteria->get('author') as $value) {
                $orX->add($queryBuilder->expr()->eq('au.name', $queryBuilder->expr()->literal($value)));
            }

            $queryBuilder->andWhere($orX);
            $criteria->remove('author');
        }

        if ($criteria->has('authorId') && !empty($criteria->get('authorId'))) {
            $orX = $queryBuilder->expr()->orX();
            foreach ((array) $criteria->get('authorId') as $value) {
                $orX->add($queryBuilder->expr()->eq('au.id', $value));
            }

            $queryBuilder->andWhere($orX);
            $criteria->remove('authorId');
        }

        if ($criteria->has('exclude_author') && !empty($criteria->get('exclude_author'))) {
            $excludedAuthors = $this->getEntityManager()
                ->createQueryBuilder()
                ->from(ArticleAuthorReference::class, 'article_author')
                ->select('aa.id')
                ->join('article_author.author', 'aaa')
                ->join('article_author.article', 'aa')
                ->where('aaa.name IN (:authors)');

            $queryBuilder->setParameter('authors', array_values((array) $criteria->get('exclude_author')));
            $queryBuilder->andWhere($queryBuilder->expr()->not($queryBuilder->expr()->in('a.id', $excludedAuthors->getQuery()->getDQL())));

            $criteria->remove('exclude_author');
        }

        if ($criteria->has('exclude_article') && !empty($criteria->get('exclude_article'))) {
            $excludedArticles = [];
            foreach ((array) $criteria->get('exclude_article') as $value) {
                if (is_numeric($value)) {
                    $excludedArticles[] = $value;
                } elseif ($value instanceof Meta and $value->getValues() instanceof ArticleInterface) {
                    $excludedArticles[] = $value->getValues()->getId();
                }
            }

            $queryBuilder->andWhere('a.id NOT IN (:excludedArticles)')
                ->setParameter('excludedArticles', $excludedArticles);
            $criteria->remove('exclude_article');
        }

        if ($criteria->has('exclude_route') && !empty($criteria->get('exclude_route'))) {
            $andX = $queryBuilder->expr()->andX();
            $andX->add($queryBuilder->expr()->notIn('a.route', (array) $criteria->get('exclude_route')));
            $queryBuilder->andWhere($andX);

            $criteria->remove('exclude_route');
        }

        if (is_array($criteria->get('route')) && !empty($criteria->get('route'))) {
            $queryBuilder->andWhere($queryBuilder->expr()->in('a.route', (array) $criteria->get('route')));

            $criteria->remove('route');
        }
    }
}
