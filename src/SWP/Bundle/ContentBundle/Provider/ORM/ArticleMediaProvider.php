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
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ArticleMediaProvider to provide media from ORM.
 */
class ArticleMediaProvider
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

    public function getRepository(): RepositoryInterface
    {
        return $this->articleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getOneById($id)
    {
        return $this->articleRepository->findOneBy(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function getOneByCriteria(Criteria $criteria): ArticleMediaInterface
    {
        $criteria->set('maxResults', 1);
        $media = $this->articleMediaRepository->getByCriteria($criteria, [])->getOneOrNullResult();
        if (null === $media) {
            throw new NotFoundHttpException('Article was not found');
        }

        return $article;
    }

    public function getManyByCriteria(Criteria $criteria): Collection
    {
        $results = $this->articleRepository->getByCriteria(
            $criteria,
            $criteria->get('order', [])
        )->getResult();

        return new ArrayCollection($results);
    }
}
