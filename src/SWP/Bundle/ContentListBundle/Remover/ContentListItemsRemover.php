<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentListBundle\Remover;

use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\ContentList\Repository\ContentListItemRepositoryInterface;

final class ContentListItemsRemover implements ContentListItemsRemoverInterface
{
    private $contentListItemRepository;

    public function __construct(ContentListItemRepositoryInterface $contentListItemRepository)
    {
        $this->contentListItemRepository = $contentListItemRepository;
    }

    public function removeContentListItems(ContentListInterface $contentList)
    {
        $this->contentListItemRepository->removeItems($contentList);
    }
}
