<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Repository;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesFeedInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;

class FacebookInstantArticlesArticleRepository extends EntityRepository implements FacebookInstantArticlesArticleRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findInFeed(FacebookInstantArticlesFeedInterface $feed, ArticleInterface $article)
    {
        return $this->getQueryByCriteria(new Criteria([
            'article' => $article,
            'feed' => $feed,
        ]), [], 'fbia')->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByArticle(ArticleInterface $article)
    {
        return $this->getQueryByCriteria(new Criteria([
            'article' => $article,
        ]), [], 'fbia')->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findSubmission(string $submissionId)
    {
        return $this->getQueryByCriteria(new Criteria([
            'submissionId' => $submissionId,
        ]), [], 'fbia')->getQuery()->getOneOrNullResult();
    }
}
