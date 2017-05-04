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

use SWP\Bundle\ContentBundle\Model\ArticleInterface as BaseArticleInterface;
use SWP\Component\ContentList\Model\ListContentInterface;
use SWP\Component\MultiTenancy\Model\OrganizationAwareInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\Rule\Model\RuleSubjectInterface;
use Takeit\Bundle\AmpHtmlBundle\Model\AmpInterface;

interface ArticleInterface extends BaseArticleInterface, TenantAwareInterface, RuleSubjectInterface, ListContentInterface, AmpInterface, OrganizationAwareInterface
{
    /**
     * @return PackageInterface
     */
    public function getPackage(): PackageInterface;

    /**
     * @param PackageInterface $package
     */
    public function setPackage(PackageInterface $package);

    /**
     * @return bool
     */
    public function isPublishedFBIA(): bool;

    /**
     * @param bool $isPublished
     */
    public function setPublishedFBIA(bool $isPublished);
}
