<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventSubscriber\Meta;

use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Repository\ContentListItemRepositoryInterface;
use SWP\Component\ContentList\Model\ContentListItemInterface;
use SWP\Component\ContentList\Model\ListContentInterface;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Event\MetaEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ArticleContentListsSubscriber implements EventSubscriberInterface
{
    private $contentListItemRepository;

    public function __construct(ContentListItemRepositoryInterface $contentListItemRepository)
    {
        $this->contentListItemRepository = $contentListItemRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            Context::META_EVENT_NAME => 'fetchContentLists',
        ];
    }

    public function fetchContentLists(MetaEvent $event)
    {
        if ('contentLists' !== $event->getPropertyName()) {
            return;
        }

        $object = $event->getData();
        if (!$object instanceof ArticleInterface || !$object instanceof ListContentInterface) {
            return;
        }

        $contentListItems = $this->contentListItemRepository->findItemsByArticle($object);
        if (0 === \count($contentListItems)) {
            return;
        }

        $contentLists = [];
        /** @var ContentListItemInterface $contentListItem */
        foreach ($contentListItems as $contentListItem) {
            if (!in_array($contentList = $contentListItem->getContentList(), $contentLists)) {
                $contentLists[] = $contentList;
            }
        }

        $event->setResult($contentLists);
    }
}
