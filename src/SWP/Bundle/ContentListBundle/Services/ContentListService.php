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

        $items = $this->contentListItemRepository->findBy(
            ['contentList' => $list],
            ['position' => 'ASC']
        );

        $byPos = [];
        $byContent = [];
        foreach ($items as $item) {
            $byPos[$item->getPosition()] = $item;
            $byContent[$item->getContent()->getId()] = $item;
        }

        switch ($action->getAction()) {
            case ContentListAction::ACTION_DELETE:
                $existing = $byContent[$contentId] ?? null;
                if (null === $existing) {
                    throw new NotFoundHttpException(sprintf(
                        'Content list item with content_id "%s" was not found on that list.',
                        $contentId
                    ));
                }
                $this->contentListItemRepository->remove($existing);

                return $existing;

            case ContentListAction::ACTION_ADD:
                if (null === $article) {
                    throw new NotFoundHttpException(sprintf(
                        'Article with id "%s" was not found.',
                        $contentId
                    ));
                }
                if (isset($byContent[$contentId])) {
                    throw new ConflictHttpException(sprintf(
                        'Article "%s" is already in this list.',
                        $contentId
                    ));
                }
                if (isset($byPos[$targetPos]) && $byPos[$targetPos]->isSticky()) {
                    throw new ConflictHttpException(
                        'Target position is held by a sticky item. Unpin it first.'
                    );
                }

                $safePos = empty($byPos) ? 0 : max(array_keys($byPos)) + 1;
                $new = $this->addArticleToContentList($list, $article, $safePos, false);
                $byPos[$safePos] = $new;
                $this->placeAt($byPos, $new, $targetPos, $targetSticky);

                return $new;

            case ContentListAction::ACTION_MOVE:
                $moving = $byContent[$contentId] ?? null;
                if (null === $moving) {
                    throw new NotFoundHttpException(sprintf(
                        'Content list item with content_id "%s" was not found on that list.',
                        $contentId
                    ));
                }
                if ($moving->isSticky() && $moving->getPosition() !== $targetPos) {
                    throw new ConflictHttpException(
                        'Sticky item cannot be moved. Unpin it first.'
                    );
                }
                $this->placeAt($byPos, $moving, $targetPos, $targetSticky);

                return $moving;
        }

        throw new \InvalidArgumentException(sprintf(
            'Unknown action "%s".',
            (string) $action->getAction()
        ));
    }

    private function placeAt(
        array &$byPos,
        ContentListItemInterface $moving,
        int $newPos,
        bool $isSticky
    ): void {
        $oldPos = $moving->getPosition();

        if ($oldPos === $newPos && $moving->isSticky() === $isSticky) {
            return;
        }

        $occupant = $byPos[$newPos] ?? null;

        if (null !== $occupant && $occupant !== $moving) {
            if ($occupant->isSticky()) {
                throw new ConflictHttpException(
                    'Target position is held by a sticky item. Unpin it first.'
                );
            }
            unset($byPos[$oldPos], $byPos[$newPos]);
            $landing = $this->nextNonStickySlot($byPos, $oldPos);
            $occupant->setPosition($landing);
            if (!$occupant->isSticky()) {
                $occupant->setStickyPosition(null);
            }
            $byPos[$landing] = $occupant;
        } else {
            unset($byPos[$oldPos]);
        }

        $moving->setPosition($newPos);
        $moving->setSticky($isSticky);
        $moving->setStickyPosition($isSticky ? $newPos : null);
        $byPos[$newPos] = $moving;
    }

    private function nextNonStickySlot(array $byPos, int $preferred): int
    {
        if (!isset($byPos[$preferred])) {
            return $preferred;
        }

        $limit = (empty($byPos) ? 0 : max(array_keys($byPos))) + 2;
        for ($delta = 1; $delta <= $limit; ++$delta) {
            $down = $preferred - $delta;
            if ($down >= 0 && !isset($byPos[$down])) {
                return $down;
            }
            $up = $preferred + $delta;
            if (!isset($byPos[$up])) {
                return $up;
            }
        }

        throw new \LogicException('Could not find a free slot.');
    }
}
