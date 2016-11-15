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
     * @param MenuItemInterface $sourceItem
     * @param MenuItemInterface $parent
     * @param int               $position
     */
    public function move(MenuItemInterface $sourceItem, MenuItemInterface $parent, int $position = 0);
}
