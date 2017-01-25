<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentListBundle\Event;

use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\ContentList\Model\ContentListItemInterface;
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
