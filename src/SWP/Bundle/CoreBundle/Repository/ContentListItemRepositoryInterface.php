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

use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\ContentListInterface;
use SWP\Component\ContentList\Repository\ContentListItemRepositoryInterface as BaseInterface;

interface ContentListItemRepositoryInterface extends BaseInterface
{
    /**
     * @param ArticleInterface     $article
     * @param ContentListInterface $list
     *
     * @return mixed
     */
    public function findItemByArticleAndListInBuckets(ArticleInterface $article, ContentListInterface $list);
}
