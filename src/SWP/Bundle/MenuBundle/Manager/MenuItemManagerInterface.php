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

namespace SWP\Bundle\MenuBundle\Manager;

use SWP\Bundle\MenuBundle\Model\MenuItemInterface;

interface MenuItemManagerInterface
{
    /**
     * Moves menu item as a first child of parent.
     *
     * @param MenuItemInterface $sourceItem
     * @param MenuItemInterface $parent
     */
    public function moveToParent(MenuItemInterface $sourceItem, MenuItemInterface $parent);

    /**
     * Moves menu item after specific menu item.
     *
     * @param MenuItemInterface $sourceItem
     * @param MenuItemInterface $afterItem
     */
    public function moveAfter(MenuItemInterface $sourceItem, MenuItemInterface $afterItem);
}
