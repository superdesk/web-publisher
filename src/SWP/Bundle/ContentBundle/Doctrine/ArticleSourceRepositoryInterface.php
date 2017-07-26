<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Doctrine;

use Doctrine\Common\Collections\Collection;
use SWP\Component\Storage\Repository\RepositoryInterface;

interface ArticleSourceRepositoryInterface extends RepositoryInterface
{
    /**
     * @return Collection
     */
    public function findSources(): Collection;
}
