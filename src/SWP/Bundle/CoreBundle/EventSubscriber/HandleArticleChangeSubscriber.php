<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Doctrine\TimestampableCancelInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\ContentListItemInterface;
use SWP\Bundle\CoreBundle\Repository\ContentListItemRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HandleArticleChangeSubscriber implements EventSubscriberInterface
{
    private $contentListItemRepository;

    private $manager;

    public function __construct(ContentListItemRepositoryInterface $contentListItemRepository, EntityManagerInterface $manager)
    {
        $this->contentListItemRepository = $contentListItemRepository;
        $this->manager = $manager;
    }

    public static function getSubscribedEvents()
    {
        return [
            ArticleEvents::POST_UPDATE => 'processArticle',
            ArticleEvents::POST_UNPUBLISH => 'processArticle',
        ];
    }

    public function processArticle(ArticleEvent $event)
    {
        $this->refreshLists($event->getArticle());
        $this->updateRoute($event->getArticle());
    }

    private function updateRoute(ArticleInterface $article)
    {
        $route = $article->getRoute();
        if (null === $route) {
            return;
        }

        $route->setArticlesUpdatedAt(new \DateTime());
        $this->manager->flush();
    }

    private function refreshLists(ArticleInterface $article)
    {
        $contentLists = [];
        $contentListItems = $this->contentListItemRepository->findItemsByArticle($article);
        /** @var ContentListItemInterface $item */
        foreach ($contentListItems as $item) {
            $contentList = $item->getContentList();
            //Removing check if article is published
            //if (!$article->isPublished()) {
            //    $this->manager->remove($item);
            //} else {
                $contentLists[$contentList->getId()] = $contentList;
            //}
            $item->getContentList()->setContentListItemsUpdatedAt(new \DateTime('now'));
            if ($contentList instanceof TimestampableCancelInterface) {
                $contentList->cancelTimestampable(true);
            }
        }
        $this->manager->flush();
        $article->setContentLists($contentLists);
    }
}
