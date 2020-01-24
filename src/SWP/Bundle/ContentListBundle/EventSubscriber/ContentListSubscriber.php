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

namespace SWP\Bundle\ContentListBundle\EventSubscriber;

use SWP\Bundle\ContentListBundle\Event\ContentListEvent;
use SWP\Bundle\ContentListBundle\Services\ContentListServiceInterface;
use SWP\Component\ContentList\ContentListEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ContentListSubscriber implements EventSubscriberInterface
{
    private $contentListService;

    public function __construct(ContentListServiceInterface $contentListService)
    {
        $this->contentListService = $contentListService;
    }

    public static function getSubscribedEvents()
    {
        return [
            ContentListEvents::POST_ITEM_ADD => 'postItemAdd',
        ];
    }

    public function postItemAdd(ContentListEvent $contentListEvent)
    {
        $this->contentListService->removeListItemsAboveTheLimit($contentListEvent->getContentList());
    }
}
