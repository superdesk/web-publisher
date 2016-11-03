<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\ContentList\Model\ContentListItemInterface;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class ContentListListener
{
    /**
     * @var ContentListRepositoryInterface
     */
    private $listRepository;

    /**
     * @var FactoryInterface
     */
    private $listItemFactory;

    /**
     * ContentListListener constructor.
     *
     * @param ContentListRepositoryInterface $listRepository
     * @param FactoryInterface               $listItemFactory
     */
    public function __construct(
        ContentListRepositoryInterface $listRepository,
        FactoryInterface $listItemFactory
    ) {
        $this->listRepository = $listRepository;
        $this->listItemFactory = $listItemFactory;
    }

    /**
     * @param ArticleEvent $event
     */
    public function onArticlePublished(ArticleEvent $event)
    {
        $article = $event->getArticle();

        $criteria = [
            'route' => $article->getRoute()->getId(),
            'author' => $article->getMetadataByKey('byline'),
            'type' => ContentListInterface::TYPE_AUTOMATIC,
            'publishedAt' => $article->getPublishedAt(),
        ];

        /** @var ContentListInterface[] $contentLists */
        $contentLists = $this->listRepository->findManyByCriteria($criteria);

        foreach ($contentLists as $contentList) {
            /** @var ContentListItemInterface $contentListItem */
            $contentListItem = $this->listItemFactory->create();
            $contentListItem->setContent($article);
            $contentList->addItem($contentListItem);
        }
    }
}
