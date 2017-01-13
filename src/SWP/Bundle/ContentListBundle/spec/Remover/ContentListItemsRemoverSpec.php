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

namespace spec\SWP\Bundle\ContentListBundle\Remover;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentListBundle\Remover\ContentListItemsRemover;
use SWP\Bundle\ContentListBundle\Remover\ContentListItemsRemoverInterface;
use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\ContentList\Repository\ContentListItemRepositoryInterface;

final class ContentListItemsRemoverSpec extends ObjectBehavior
{
    public function let(ContentListItemRepositoryInterface $contentListItemRepository)
    {
        $this->beConstructedWith($contentListItemRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ContentListItemsRemover::class);
    }

    public function it_should_implement_interface()
    {
        $this->shouldImplement(ContentListItemsRemoverInterface::class);
    }

    public function it_should_remove_content_list_items(
        ContentListInterface $contentList,
        ContentListItemRepositoryInterface $contentListItemRepository
    ) {
        $contentListItemRepository->removeItems($contentList);

        $this->removeContentListItems($contentList);
    }
}
