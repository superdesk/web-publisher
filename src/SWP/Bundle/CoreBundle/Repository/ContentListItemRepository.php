<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use SWP\Bundle\ContentListBundle\Doctrine\ORM\ContentListItemRepository as BaseRepository;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\ContentListInterface;
use SWP\Bundle\CoreBundle\Model\ContentListItemInterface;
use SWP\Component\Common\Criteria\Criteria;

class ContentListItemRepository extends BaseRepository implements ContentListItemRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findItemByArticleAndList(
        ArticleInterface $article,
        ContentListInterface $list,
        string $type = ContentListInterface::TYPE_BUCKET
    ): ?ContentListItemInterface {
        $queryBuilder = $this->createQueryBuilder('cl');

        return $queryBuilder
            ->leftJoin('cl.contentList', 'l')
            ->leftJoin('cl.content', 'c')
            ->where('l.type = :type')
            ->andWhere('c.id = :article')
            ->andWhere('l.id = :list')
            ->setParameters([
                'article' => $article->getId(),
                'list' => $list->getId(),
                'type' => $type,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getItemsTitlesByList(ContentListInterface $list): array
    {
        $query = $this->getEntityManager()->createQuery("
            SELECT 
                 partial c.{id, title} 
            from 
                SWP\Bundle\CoreBundle\Model\ContentListItem cl
            LEFT JOIN
                SWP\Bundle\CoreBundle\Model\ContentList l 
            WITH 
                l.id = cl.contentList
            LEFT JOIN 
                SWP\Bundle\CoreBundle\Model\Article c
            WITH
                c.id = cl.content
            WHERE
                l.id = :list 
            AND
                c.status = :status
            ORDER BY 
                cl.position ASC
        ")
            ->setParameters([
                'list' => $list->getId(),
                'status' => ArticleInterface::STATUS_PUBLISHED,
            ]);

        $query->setMaxResults(5);
        $query->setFirstResult(0);

        return $query->getArrayResult();
    }

    public function findItemsByArticle(ArticleInterface $article): array
    {
        $queryBuilder = $this->createQueryBuilder('cl');

        return $queryBuilder
            ->join('cl.contentList', 'l')
            ->addSelect('l', 'c', 'm', 'i', 'r', 'ri')
            ->leftJoin('cl.content', 'c')
            ->leftJoin('c.media', 'm')
            ->leftJoin('m.image', 'i')
            ->leftJoin('m.renditions', 'r')
            ->leftJoin('r.image', 'ri')
            ->andWhere('c.id = :article')
            ->andWhere('l.deletedAt IS NULL')
            ->setParameters([
                'article' => $article->getId(),
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    protected function applyCriteria(QueryBuilder $queryBuilder, Criteria $criteria, string $alias)
    {
        $queryBuilder
            ->leftJoin('n.content', 'a')->addSelect('a')
            ->leftJoin('a.articleStatistics', 'stats')
            ->leftJoin('a.externalArticle', 'ext')
            ->addSelect('stats', 'ext');
        //Remove limit for listing only published articles
        //$queryBuilder->andWhere($queryBuilder->expr()->eq('a.status', $queryBuilder->expr()->literal(ArticleInterface::STATUS_PUBLISHED)));

        parent::applyCriteria($queryBuilder, $criteria, $alias);
    }
}
