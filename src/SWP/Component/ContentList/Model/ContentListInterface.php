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

use Doctrine\Common\Collections\Collection;
use SWP\Component\Common\Model\EnableableInterface;
use SWP\Component\Common\Model\SoftDeletableInterface;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface ContentListInterface extends TimestampableInterface, SoftDeletableInterface, PersistableInterface, EnableableInterface
{
    /**
     * Items are added autmatically based on provided criteria.
     * On criteria change items are also changed.
     */
    const TYPE_AUTOMATIC = 'automatic';

    /**
     * Manually add (and reorder) items.
     */
    const TYPE_MANUAL = 'manual';

    /**
     * Useful for collecting items. Items are added automatically.
     */
    const TYPE_BUCKET = 'bucket';

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return null|string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName(string $name);

    /**
     * @return null|string
     */
    public function getDescription();

    /**
     * @param string|null $description
     */
    public function setDescription(string $description = null);

    /**
     * @return null|string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType(string $type);

    /**
     * @return null|int
     */
    public function getCacheLifeTime();

    /**
     * @param int $cacheLifeTime
     */
    public function setCacheLifeTime(int $cacheLifeTime = 0);

    /**
     * @return null|int
     */
    public function getLimit();

    /**
     * @param int $limit
     */
    public function setLimit(int $limit);

    /**
     * @return Collection|ContentListItemInterface[]
     */
    public function getItems();

    /**
     * @param Collection|ContentListItemInterface[] $items
     */
    public function setItems($items);

    /**
     * @param ContentListItemInterface $item
     */
    public function addItem(ContentListItemInterface $item);

    /**
     * @param ContentListItemInterface $item
     */
    public function hasItem(ContentListItemInterface $item);

    /**
     * @param ContentListItemInterface $item
     */
    public function removeItem(ContentListItemInterface $item);

    /**
     * @return array
     */
    public function getFilters();

    /**
     * @param array $filters
     */
    public function setFilters(array $filters);

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getFilter(string $key);
}
