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

namespace SWP\Bundle\CoreBundle\Event;

use SWP\Bundle\CoreBundle\Model\ContentListInterface;
use SWP\Bundle\CoreBundle\Model\ContentListItemInterface;
use Symfony\Component\EventDispatcher\Event;

class ContentListEvent extends Event
{
    /**
     * @var ContentListInterface
     */
    protected $contentList;

    /**
     * @var ContentListItemInterface
     */
    protected $item;

    /**
     * ContentListEvent constructor.
     *
     * @param ContentListInterface     $contentList
     * @param ContentListItemInterface $item
     */
    public function __construct(ContentListInterface $contentList, ContentListItemInterface $item)
    {
        $this->contentList = $contentList;
        $this->item = $item;
    }

    /**
     * @return ContentListInterface
     */
    public function getContentList(): ContentListInterface
    {
        return $this->contentList;
    }

    /**
     * @return ContentListItemInterface
     */
    public function getItem(): ContentListItemInterface
    {
        return $this->item;
    }
}
