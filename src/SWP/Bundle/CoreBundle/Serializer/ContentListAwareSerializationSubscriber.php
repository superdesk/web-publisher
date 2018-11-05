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

namespace SWP\Bundle\CoreBundle\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\ContentListItemInterface;
use SWP\Bundle\CoreBundle\Repository\ContentListItemRepositoryInterface;
use SWP\Component\ContentList\Model\ListContentInterface;

final class ContentListAwareSerializationSubscriber implements EventSubscriberInterface
{
    private $contentListItemRepository;

    public function __construct(ContentListItemRepositoryInterface $contentListItemRepository)
    {
        $this->contentListItemRepository = $contentListItemRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'class' => Article::class,
                'method' => 'onPreSerialize',
            ],
        ];
    }

    public function onPreSerialize(ObjectEvent $event)
    {
        $object = $event->getObject();
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

        $object->setContentLists($contentLists);
    }
}
