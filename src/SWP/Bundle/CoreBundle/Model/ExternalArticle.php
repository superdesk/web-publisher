<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Component\MultiTenancy\Model\TenantAwareTrait;
use SWP\Component\OutputChannel\Model\ExternalArticle as BaseExternalArticle;

class ExternalArticle extends BaseExternalArticle implements ExternalArticleInterface
{
    use TenantAwareTrait;

    /**
     * @var TenantInterface
     */
    protected $tenant;

    /**
     * @var ArticleInterface
     */
    protected $article;

    /**
     * {@inheritdoc}
     */
    public function __construct(ArticleInterface $article, string $externalId, string $status)
    {
        $this->setArticle($article);
        $this->setExternalId($externalId);
        $this->setStatus($status);

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getArticle(): ArticleInterface
    {
        return $this->article;
    }

    /**
     * {@inheritdoc}
     */
    public function setArticle(ArticleInterface $article): void
    {
        $this->article = $article;
        $article->setExternalArticle($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getTenant(): TenantInterface
    {
        return $this->tenant;
    }

    /**
     * {@inheritdoc}
     */
    public function setTenant(TenantInterface $tenant): void
    {
        $this->tenant = $tenant;
    }
}
