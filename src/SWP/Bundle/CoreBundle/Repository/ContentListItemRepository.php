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
    ) {
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

    /**
     * {@inheritdoc}
     */
    protected function applyCriteria(QueryBuilder $queryBuilder, Criteria $criteria, string $alias)
    {
        $queryBuilder->leftJoin('n.content', 'a')
            ->addSelect('a');

        $queryBuilder->andWhere($queryBuilder->expr()->eq('a.status', $queryBuilder->expr()->literal(ArticleInterface::STATUS_PUBLISHED)));

        parent::applyCriteria($queryBuilder, $criteria, $alias);
    }
}
