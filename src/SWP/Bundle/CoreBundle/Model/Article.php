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

use SWP\Bundle\ContentBundle\Model\Article as BaseArticle;
use SWP\Component\MultiTenancy\Model\OrganizationAwareTrait;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;

class Article extends BaseArticle implements ArticleInterface
{
    use TenantAwareTrait, OrganizationAwareTrait;

    /**
     * @var PackageInterface
     */
    protected $package;

    /**
     * {@inheritdoc}
     */
    public function getPackage(): PackageInterface
    {
        return $this->package;
    }

    /**
     * {@inheritdoc}
     */
    public function setPackage(PackageInterface $package)
    {
        $this->package = $package;
    }
}
