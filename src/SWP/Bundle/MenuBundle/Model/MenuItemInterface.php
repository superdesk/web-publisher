<?php

namespace SWP\Bundle\MenuBundle\Model;

use Knp\Menu\NodeInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface MenuItemInterface extends NodeInterface, PersistableInterface
{
    /**
     * @return MenuItemInterface|null
     */
    public function getRoot();
}
