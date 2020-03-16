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
use SWP\Bundle\ContentBundle\Doctrine\ArticleMediaRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;

class ArticleMediaRepository extends EntityRepository implements ArticleMediaRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getByCriteria(Criteria $criteria, array $sorting): QueryBuilder
    {
        $qb = $this->getQueryByCriteria($criteria, $sorting, 'am');

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function findMediaByAssetId(string $assetId)
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.image', 'i')
            ->leftJoin('m.file', 'f')
            ->where('i.assetId = :assetId')
            ->orWhere('f.assetId = :assetId')
            ->setParameter('assetId', $assetId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findEmbeddedImagesAndFeatureMediaByArticle(ArticleInterface $article): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.article = :article')
            ->andWhere('m.key != :key')
            ->setParameter('article', $article->getId())
            ->setParameter('key', ArticleMediaInterface::TYPE_SLIDE_SHOW)
            ->getQuery()
            ->getResult();
    }
}
