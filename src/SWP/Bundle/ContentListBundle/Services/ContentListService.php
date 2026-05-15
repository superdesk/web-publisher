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
use SWP\Component\ContentList\Model\ContentListAction;
use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\ContentList\Model\ContentListItemInterface;
use SWP\Component\ContentList\Model\ListContentInterface;
use SWP\Component\ContentList\Repository\ContentListItemRepositoryInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            new ContentListEvent($contentList, $contentListItem),
            ContentListEvents::POST_ITEM_ADD
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

    public function applyManualListAction(
        ContentListInterface $list,
        ContentListAction $action,
        ?ArticleInterface $article = null
    ): ContentListItemInterface {
        $contentId = $action->getContentId();
        $targetPos = $action->getPosition();
        $targetSticky = $action->isSticky();

        $existing = $this->contentListItemRepository->findOneBy([
            'contentList' => $list,
            'content' => $contentId,
        ]);

        switch ($action->getAction()) {
            case ContentListAction::ACTION_DELETE:
                if (null === $existing) {
                    throw new NotFoundHttpException(sprintf(
                        'Content list item with content_id "%s" was not found on that list.',
                        $contentId
                    ));
                }
                $this->contentListItemRepository->remove($existing);
                $this->reconcileStickyState($list);

                return $existing;

            case ContentListAction::ACTION_ADD:
                if (null === $article) {
                    throw new NotFoundHttpException(sprintf(
                        'Article with id "%s" was not found.',
                        $contentId
                    ));
                }
                if (null !== $existing) {
                    throw new ConflictHttpException(sprintf(
                        'Article "%s" is already in this list.',
                        $contentId
                    ));
                }
                $occupant = $this->contentListItemRepository->findOneBy([
                    'contentList' => $list,
                    'position' => $targetPos,
                ]);
                if (null !== $occupant && $occupant->isSticky()) {
                    throw new ConflictHttpException(
                        'Target position is held by a sticky item. Unpin it first.'
                    );
                }

                $new = $this->addArticleToContentList($list, $article, $targetPos, false);
                if ($targetSticky) {
                    $new->setSticky(true);
                    $new->setStickyPosition($targetPos);
                    $this->contentListItemRepository->flush();
                }
                $this->reconcileStickyState($list);

                return $new;

            case ContentListAction::ACTION_MOVE:
                if (null === $existing) {
                    throw new NotFoundHttpException(sprintf(
                        'Content list item with content_id "%s" was not found on that list.',
                        $contentId
                    ));
                }
                if ($existing->isSticky() && $existing->getPosition() !== $targetPos) {
                    throw new ConflictHttpException(
                        'Sticky item cannot be moved. Unpin it first.'
                    );
                }
                $occupant = $this->contentListItemRepository->findOneBy([
                    'contentList' => $list,
                    'position' => $targetPos,
                ]);
                if (null !== $occupant && $occupant !== $existing && $occupant->isSticky()) {
                    throw new ConflictHttpException(
                        'Target position is held by a sticky item. Unpin it first.'
                    );
                }
                $existing->setPosition($targetPos);
                $existing->setSticky($targetSticky);
                $existing->setStickyPosition($targetSticky ? $targetPos : null);
                $this->contentListItemRepository->flush();
                $this->reconcileStickyState($list);

                return $existing;
        }

        throw new \InvalidArgumentException(sprintf(
            'Unknown action "%s".',
            (string) $action->getAction()
        ));
    }

    /**
     * Restores sticky items to their target slot after a Gedmo Sortable shift
     * (which is unaware of stickiness) and clears stale sticky_position
     * fossils on non-sticky items so the manual-list view stays consistent.
     *
     * Stickies are processed in an order that prevents one restoration from
     * bumping another unrestored sticky out of place: up-movers in descending
     * target order, down-movers in ascending target order. Within a single
     * Gedmo relocation cycle (which always shifts a contiguous range by ±1),
     * this guarantees the next sticky's current position is never inside the
     * range affected by the previous restore.
     */
    private function reconcileStickyState(ContentListInterface $list): void
    {
        $items = $this->contentListItemRepository->findBy(['contentList' => $list]);

        $cleanupFlush = false;
        foreach ($items as $item) {
            if (!$item->isSticky() && null !== $item->getStickyPosition()) {
                $item->setStickyPosition(null);
                $cleanupFlush = true;
            }
        }
        if ($cleanupFlush) {
            $this->contentListItemRepository->flush();
        }

        $stickyItems = $this->contentListItemRepository->findBy([
            'sticky' => true,
            'contentList' => $list,
        ]);

        $upMovers = [];
        $downMovers = [];
        foreach ($stickyItems as $item) {
            $target = $item->getStickyPosition();
            if (null === $target || $item->getPosition() === $target) {
                continue;
            }
            if ($item->getPosition() < $target) {
                $upMovers[] = $item;
            } else {
                $downMovers[] = $item;
            }
        }

        usort($upMovers, static fn ($a, $b) => $b->getStickyPosition() <=> $a->getStickyPosition());
        usort($downMovers, static fn ($a, $b) => $a->getStickyPosition() <=> $b->getStickyPosition());

        foreach (array_merge($upMovers, $downMovers) as $item) {
            $item->setPosition($item->getStickyPosition());
            $this->contentListItemRepository->flush();
        }
    }
}
