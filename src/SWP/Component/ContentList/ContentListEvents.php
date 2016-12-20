<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\ContentList;

final class ContentListEvents
{
    /**
     * The LIST_CRITERIA_CHANGE event occurs after list criteria are changed.
     *
     * @Event("SWP\Bundle\ContentBundle\Event\ContentListEvent")
     *
     * @var string
     */
    const LIST_CRITERIA_CHANGE = 'swp.list_criteria_change';

    /**
     * The POST_ITEM_ADD event occurs after adding new item to list .
     *
     * This event allows you to work with manually or automatically added list item.
     *
     * @Event("SWP\Bundle\ContentBundle\Event\ContentListEvent")
     *
     * @var string
     */
    const POST_ITEM_ADD = 'swp.content_list.post_item_add';

    private function __construct()
    {
    }
}
