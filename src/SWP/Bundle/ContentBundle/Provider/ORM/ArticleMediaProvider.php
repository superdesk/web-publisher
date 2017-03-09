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

use Doctrine\ORM\Tools\Pagination\Paginator;
use SWP\Bundle\ContentBundle\Doctrine\ArticleMediaRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Provider\AbstractProvider;
use SWP\Bundle\ContentBundle\Provider\ArticleMediaProviderInterface;
use SWP\Component\Common\Criteria\Criteria;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * ArticleMediaProvider to provide media from ORM.
 */
class ArticleMediaProvider extends AbstractProvider implements ArticleMediaProviderInterface
{
    /**
     * @var ArticleMediaRepositoryInterface
     */
    private $articleMediaRepository;

    /**
     * @var array
     */
    private $internalCache = [];

    /**
     * ArticleMediaProvider constructor.
     *
     * @param ArticleMediaRepositoryInterface $articleMediaRepository
     */
    public function __construct(
        ArticleMediaRepositoryInterface $articleMediaRepository
    ) {
        $this->articleMediaRepository = $articleMediaRepository;
    }

    /**
     * @return ArticleMediaRepositoryInterface
     */
    public function getRepository(): ArticleMediaRepositoryInterface
    {
        return $this->articleMediaRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getOneById($id)
    {
        return $this->getRepository()->findOneBy(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function getOneByCriteria(Criteria $criteria): ArticleMediaInterface
    {
        $criteria->set('maxResults', 1);
        if (null !== $result = $this->getFromInternalCache($criteria)) {
            return $result;
        }

        $media = $this->getRepository()->getByCriteria($criteria, [])->getQuery()->getOneOrNullResult();
        if (null === $media) {
            throw new NotFoundHttpException('Media was not found');
        }
        $this->addToInternalCache($criteria, $media);

        return $media;
    }

    /**
     * {@inheritdoc}
     */
    public function getManyByCriteria(Criteria $criteria): Collection
    {
        if (null !== $result = $this->getFromInternalCache($criteria)) {
            return $result;
        }

        $query = $this->getRepository()->getByCriteria(
            $criteria,
            $criteria->get('order', [])
        )
        ->addSelect('r')
        ->leftJoin('am.renditions', 'r')
        ->addSelect('i')
        ->leftJoin('r.image', 'i')
        ->getQuery();

        $paginator = new Paginator($query, $fetchJoinCollection = true);

        $result = new ArrayCollection(iterator_to_array($paginator->getIterator()));
        $this->addToInternalCache($criteria, $result);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountByCriteria(Criteria $criteria): int
    {
        return (int) $this->getRepository()->getByCriteria(
            $criteria,
            $criteria->get('order', [])
        )
            ->select('COUNT(am.id)')
            ->setFirstResult(null)
            ->setMaxResults(null)
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getFromInternalCache(Criteria $criteria)
    {
        $key = $this->getKeyForCriteria($criteria);
        if (array_key_exists($key, $this->internalCache)) {
            return $this->internalCache[$key];
        }

        return;
    }

    private function addToInternalCache(Criteria $criteria, $value)
    {
        $key = $this->getKeyForCriteria($criteria);
        $this->internalCache[$key] = $value;
    }

    private function getKeyForCriteria(Criteria $criteria): string
    {
        return md5(serialize($criteria->all()));
    }
}
