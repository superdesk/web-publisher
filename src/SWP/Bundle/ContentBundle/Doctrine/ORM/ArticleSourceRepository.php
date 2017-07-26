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

namespace SWP\Bundle\ContentBundle\Doctrine\ORM;

use Doctrine\Common\Collections\Collection;
use SWP\Bundle\ContentBundle\Doctrine\ArticleSourceRepositoryInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;

class ArticleSourceRepository extends EntityRepository implements ArticleSourceRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findSources(): Collection
    {
        // TODO: Implement findSources() method.
    }
}
