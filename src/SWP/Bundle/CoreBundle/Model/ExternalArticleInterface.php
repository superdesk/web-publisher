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

use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\OutputChannel\Model\ExternalArticleInterface as BaseExternalArticleInterface;

interface ExternalArticleInterface extends BaseExternalArticleInterface, TenantAwareInterface
{
    /**
     * ExternalArticleInterface constructor.
     *
     * @param ArticleInterface $article
     * @param string           $externalId
     * @param string           $status
     */
    public function __construct(ArticleInterface $article, string $externalId, string $status);

    /**
     * @return ArticleInterface
     */
    public function getArticle(): ArticleInterface;

    /**
     * @param ArticleInterface $article
     */
    public function setArticle(ArticleInterface $article): void;

    /**
     * @return TenantInterface
     */
    public function getTenant(): TenantInterface;

    /**
     * @param TenantInterface $tenant
     */
    public function setTenant(TenantInterface $tenant): void;
}
