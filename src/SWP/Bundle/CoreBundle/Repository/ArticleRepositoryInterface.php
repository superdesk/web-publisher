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
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface as BaseInterface;
use SWP\Bundle\CoreBundle\Model\PackageInterface;

interface ArticleRepositoryInterface extends BaseInterface
{
    /**
     * @param string           $slug
     * @param PackageInterface $package
     *
     * @return QueryBuilder
     */
    public function getArticleBySlugForPackage(string $slug, PackageInterface $package): QueryBuilder;
}
