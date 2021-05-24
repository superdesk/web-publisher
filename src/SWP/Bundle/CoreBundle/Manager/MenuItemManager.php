<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\Factory\ExtensionInterface;
use SWP\Bundle\ContentBundle\Model\RouteAwareInterface;
use SWP\Bundle\MenuBundle\Doctrine\MenuItemRepositoryInterface;
use SWP\Bundle\MenuBundle\Manager\MenuItemManager as BaseMenuItemManager;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;

final class MenuItemManager extends BaseMenuItemManager
{
    /**
     * MenuItemManager constructor.
     */
    public function __construct(
        MenuItemRepositoryInterface $menuItemRepository,
        EntityManagerInterface $objectManager,
        ExtensionInterface $extensionsChain
    ) {
        parent::__construct($menuItemRepository, $objectManager, $extensionsChain);
    }

    /**
     * {@inheritdoc}
     */
    public function update(MenuItemInterface $menu)
    {
        $options = [];

        if ($menu instanceof RouteAwareInterface) {
            $options = [
                'route' => $menu->getRoute() ? $menu->getRoute()->getName() : null,
            ];
        }

        $this->updateOptions($menu, $options);
    }
}
