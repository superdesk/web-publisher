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

namespace SWP\Bundle\ContentBundle\Provider\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use function iterator_to_array;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Provider\ArticleProviderInterface;
use SWP\Component\Common\Criteria\Criteria;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ArticleProvider to provide articles from ORM.
 */
class ArticleProvider implements ArticleProviderInterface
{
    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * ArticleProvider constructor.
     */
    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getOneById($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            return $this->articleRepository->findOneBySlug($id);
        }

        return $this->articleRepository->findOneBy(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent($id)
    {
        return $this->articleRepository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteArticlesQuery(string $routeIdentifier, array $order)
    {
        return $this->articleRepository->getQueryForRouteArticles($routeIdentifier, $order);
    }

    /**
     * {@inheritdoc}
     */
    public function getOneByCriteria(Criteria $criteria): ArticleInterface
    {
        // set max results to null to not break joins
        $criteria->set('maxResults', null);
        $query = $this->articleRepository->getByCriteria($criteria, [])->getQuery();
        $query->setMaxResults(1);
        $query->setFirstResult(0);
        $articles = iterator_to_array(new Paginator($query, $fetchJoin = true));
        if (null === $articles || 0 === count($articles)) {
            throw new NotFoundHttpException('Article was not found');
        }

        return $articles[0];
    }

    /**
     * {@inheritdoc}
     */
    public function getManyByCriteria(Criteria $criteria, array $sorting): Collection
    {
        $articles = $this->articleRepository->getArticlesByCriteria($criteria, $sorting)->getQuery()->getResult();

        return new ArrayCollection($articles);
    }

    /**
     * {@inheritdoc}
     */
    public function getCountByCriteria(Criteria $criteria): int
    {
        return $this->articleRepository->countByCriteria($criteria);
    }
}
