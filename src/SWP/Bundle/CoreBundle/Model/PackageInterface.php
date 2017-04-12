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

namespace SWP\Bundle\CoreBundle\Model;

use Doctrine\Common\Collections\Collection;
use SWP\Component\Bridge\Model\PackageInterface as BasePackageInterface;
use SWP\Component\MultiTenancy\Model\OrganizationAwareInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;

interface PackageInterface extends BasePackageInterface, OrganizationAwareInterface, RuleSubjectInterface
{
    /**
     * @return ArticleInterface[]|Collection
     */
    public function getArticles(): Collection;

    /**
     * @param ArticleInterface $article
     */
    public function removeArticle(ArticleInterface $article);

    /**
     * @param ArticleInterface $article
     */
    public function addArticle(ArticleInterface $article);

    /**
     * @param ArticleInterface $article
     */
    public function hasArticle(ArticleInterface $article);

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @param string $status
     */
    public function setStatus(string $status);
}
