<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle;

class ContentListEvents
{
    /**
     * The POST_ADD event occurs after adding new item to list .
     *
     * This event allows you to work with manually or automatically added list item.
     *
     * @Event("SWP\Bundle\ContentBundle\Event\ContentListEvent")
     *
     * @var string
     */
    const POST_ITEM_ADD = 'swp.content_list.post_item_add';
}
