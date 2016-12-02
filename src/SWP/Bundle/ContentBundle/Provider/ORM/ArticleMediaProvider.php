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

use SWP\Bundle\ContentBundle\Doctrine\ArticleMediaRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Provider\AbstractProvider;
use SWP\Bundle\ContentBundle\Provider\ArticleMediaProviderInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ArticleMediaProvider to provide media from ORM.
 */
class ArticleMediaProvider extends AbstractProvider implements ArticleMediaProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $articleMediaRepository;

    /**
     * ArticleProvider constructor.
     *
     * @param RepositoryInterface $articleMediaRepository
     */
    public function __construct(
        RepositoryInterface $articleMediaRepository
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
        $media = $this->getRepository()->getByCriteria($criteria, [])->getQuery()->getOneOrNullResult();
        if (null === $media) {
            throw new NotFoundHttpException('Media was not found');
        }

        return $media;
    }

    public function getCountByCriteria(Criteria $criteria) : int
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
}
