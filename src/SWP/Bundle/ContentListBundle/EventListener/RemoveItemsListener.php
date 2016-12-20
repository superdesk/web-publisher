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

namespace SWP\Bundle\ContentListBundle\EventListener;

use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentListBundle\Remover\ContentListItemsRemoverInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\ContentList\Model\ContentListItemInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class RemoveItemsListener
{
    private $contentListItemsRemover;

    private $articleRepository;

    private $contentListItemFactory;

    public function __construct(
        ContentListItemsRemoverInterface $contentListItemsRemover,
        ArticleRepositoryInterface $articleRepository,
        FactoryInterface $contentListItemFactory
    ) {
        $this->contentListItemsRemover = $contentListItemsRemover;
        $this->articleRepository = $articleRepository;
        $this->contentListItemFactory = $contentListItemFactory;
    }

    public function onListCriteriaChange(GenericEvent $event)
    {
        $contentList = $event->getSubject();
        if (!$contentList instanceof ContentListInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Expected argument of type "%s", "%s" given.',
                ContentListInterface::class,
                is_object($contentList) ? get_class($contentList) : gettype($contentList))
            );
        }

        if ($contentList->getFilters() !== $event->getArgument('filters')) {
            $this->contentListItemsRemover->removeContentListItems($contentList);
            // find max 100 articles matching criteria and insert it into the list
            $filters = json_decode($contentList->getFilters(), true);

            // validate datetime parameters
            // throw exception when datetime is not valid
        //$beforeDate = new \DateTime($filters['publishedBefore']);
        //$filters['publishedBefore'] = $beforeDate->format('Y-m-d');
            //$publishedAt = new \DateTime($filters['publishedAt']);
            //$filters['publishedAt'] = $publishedAt->format('Y-m-d');
            //$afterDate = new \DateTime($filters['publishedAfter']);
        //$filters['publishedAfter'] = $afterDate->format('Y-m-d');

            $filters['maxResults'] = 0 === $contentList->getLimit() || null === $contentList->getLimit() ? 100 : $contentList->getLimit();

            // replace with content list content provider
            // getContentListContentItem(int $limit);
            $articles = $this->articleRepository->findArticlesByCriteria(new Criteria($filters), [], new PaginationData());
            $position = 0;
            foreach ($articles as $article) {
                /** @var ContentListItemInterface $contentListItem */
                $contentListItem = $this->contentListItemFactory->create();
                $contentListItem->setContent($article);
                $contentListItem->setPosition($position);
                $contentList->addItem($contentListItem);
                ++$position;
            }
        }
    }
}
