<?php

namespace SWP\Bundle\MenuBundle\Model;

use SWP\Component\Storage\Model\PersistableInterface;

interface MenuItemInterface extends PersistableInterface
{
    /**
     * @return MenuItemInterface|null
     */
    public function getRoot();
}
