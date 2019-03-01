<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

use Doctrine\Common\Collections\Collection;

class RelatedArticleListItem
{
    /**
     * @var TenantInterface[]|Collection
     */
    private $tenants;

    /**
     * @var string
     */
    private $title;

    public function getTenants(): Collection
    {
        return $this->tenants;
    }

    public function setTenants($tenants): void
    {
        $this->tenants = $tenants;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
