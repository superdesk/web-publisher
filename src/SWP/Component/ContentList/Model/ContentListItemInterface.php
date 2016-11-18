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

namespace SWP\Component\ContentList\Model;

use SWP\Component\Common\Model\EnableableInterface;
use SWP\Component\Common\Model\SoftDeletableInterface;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface ContentListItemInterface extends
    TimestampableInterface,
    SoftDeletableInterface,
    PersistableInterface,
    EnableableInterface
{
    /**
     * @return int|null
     */
    public function getPosition();

    /**
     * @param int $position
     */
    public function setPosition(int $position);

    /**
     * @return ContentListInterface
     */
    public function getContentList(): ContentListInterface;

    /**
     * @param null|ContentListInterface $contentList
     */
    public function setContentList(ContentListInterface $contentList = null);

    /**
     * @return ListContentInterface
     */
    public function getContent(): ListContentInterface;

    /**
     * @param ListContentInterface $content
     */
    public function setContent(ListContentInterface $content);

    /**
     * @return bool
     */
    public function isSticky(): bool;

    /**
     * @param bool $sticky
     */
    public function setSticky(bool $sticky);
}
