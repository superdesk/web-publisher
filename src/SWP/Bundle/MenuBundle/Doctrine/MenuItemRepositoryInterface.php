<?php

declare(strict_types=1);

namespace SWP\Bundle\MenuBundle\Doctrine;

use SWP\Bundle\MenuBundle\Model\MenuItemInterface;

interface MenuItemRepositoryInterface
{
    /**
     * @param string $name
     *
     * @return MenuItemInterface|null
     */
    public function getOneMenuItemByName(string $name);

    /**
     * @param int $id
     *
     * @return MenuItemInterface|null
     */
    public function getOneMenuItemById(int $id);
}
