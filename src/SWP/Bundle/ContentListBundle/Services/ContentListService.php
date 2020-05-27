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

namespace SWP\Bundle\ContentListBundle\Services;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentListBundle\Event\ContentListEvent;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\ContentList\ContentListEvents;
use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\ContentList\Model\ContentListItemInterface;
use SWP\Component\ContentList\Model\ListContentInterface;
use SWP\Component\ContentList\Repository\ContentListItemRepositoryInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ContentListService implements ContentListServiceInterface
{
    private $eventDispatcher;

    private $listItemFactory;

    private $contentListItemRepository;

    public function __construct(EventDispatcherInterface $eventDispatcher, FactoryInterface $listItemFactory, ContentListItemRepositoryInterface $contentListItemRepository)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->listItemFactory = $listItemFactory;
        $this->contentListItemRepository = $contentListItemRepository;
    }

    public function addArticleToContentList(ContentListInterface $contentList, ArticleInterface $article, $position = null, bool $isSticky = false): ContentListItemInterface
    {
        /* @var ContentListItemInterface $contentListItem */
        $contentListItem = $this->listItemFactory->create();

        if ($article instanceof ListContentInterface) {
            $contentListItem->setContent($article);
        }

        if (null === $position) {
            $position = $contentList->getItems()->count();
        }

        $contentListItem->setPosition((int) $position);
        $contentListItem->setContentList($contentList);
        $this->contentListItemRepository->add($contentListItem);

        $this->eventDispatcher->dispatch(
            ContentListEvents::POST_ITEM_ADD,
            new ContentListEvent($contentList, $contentListItem)
        );
        $contentList->setUpdatedAt(new \DateTime());

        $this->toggleStickOnItemPosition($contentListItem, $isSticky, (int) $position);

        return $contentListItem;
    }

    public function removeListItemsAboveTheLimit(ContentListInterface $contentList): void
    {
        $items = $this->contentListItemRepository
            ->getSortedItems(new Criteria(['contentList' => $contentList]), [], ['contentList' => $contentList])
            ->setMaxResults(null)
            ->getQuery()
            ->getResult();

        if (null !== $contentList->getLimit() && $contentList->getLimit() > 0 && \count($items) > $contentList->getLimit()) {
            foreach ($items as $key => $item) {
                if ($key + 1 > $contentList->getLimit()) {
                    $this->contentListItemRepository->remove($item);
                }
            }
        }
    }

    public function toggleStickOnItemPosition(ContentListItemInterface $contentListItem, bool $isSticky, int $position): void
    {
        $contentListItem->setSticky($isSticky);

        if ($contentListItem->isSticky()) {
            $contentListItem->setStickyPosition($position);
        }
    }

    public function repositionStickyItems(ContentListInterface $contentList): void
    {
        $stickyItems = $this->contentListItemRepository->findBy([
            'sticky' => true,
            'contentList' => $contentList,
        ]);

        foreach ($stickyItems as $stickyItem) {
            if (null !== $stickyItem->getStickyPosition()) {
                $stickyItem->setPosition($stickyItem->getStickyPosition());
            }
        }
    }

    public function isAnyItemPinnedOnPosition(int $listId, int $position): ?ContentListItemInterface
    {
        return $this->contentListItemRepository->findOneBy([
            'contentList' => $listId,
            'position' => $position,
        ]);
    }
}
