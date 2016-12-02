<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Menu Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MenuBundle\Model;

use Knp\Menu\ItemInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface MenuItemInterface extends ItemInterface, PersistableInterface
{
    /**
     * @return int
     */
    public function getLeft(): int;

    /**
     * @param int $left
     */
    public function setLeft(int $left);

    /**
     * @return int
     */
    public function getRight(): int;

    /**
     * @param int $right
     */
    public function setRight(int $right);

    /**
     * @return int
     */
    public function getPosition(): int;

    /**
     * @param int $position
     */
    public function setPosition(int $position);
}
