<?php

/*
 * This file is part of the Superdesk Web Publisher Menu Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\MenuBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\MenuBundle\Doctrine\MenuItemRepositoryInterface;
use SWP\Bundle\MenuBundle\Manager\MenuItemManager;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\MenuBundle\Manager\MenuItemManagerInterface;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;

/**
 * @mixin MenuItemManager
 */
final class MenuItemManagerSpec extends ObjectBehavior
{
    public function let(MenuItemRepositoryInterface $menuItemRepository, ObjectManager $objectManager)
    {
        $this->beConstructedWith($menuItemRepository, $objectManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(MenuItemManager::class);
    }

    public function it_implements_an_interface()
    {
        $this->shouldImplement(MenuItemManagerInterface::class);
    }

    public function it_moves_source_item_making_it_first_child_of_parent(
        MenuItemInterface $sourceItem,
        MenuItemInterface $parentItem,
        MenuItemRepositoryInterface $menuItemRepository,
        ObjectManager $objectManager
    ) {
        $menuItemRepository->persistAsFirstChildOf($sourceItem, $parentItem)->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $this->moveToParent($sourceItem, $parentItem);
    }

    public function it_moves_source_item_after_specific_item(
        MenuItemInterface $sourceItem,
        MenuItemInterface $afterItem,
        MenuItemRepositoryInterface $menuItemRepository,
        ObjectManager $objectManager
    ) {
        $menuItemRepository->persistAsNextSiblingOf($sourceItem, $afterItem)->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $this->moveAfter($sourceItem, $afterItem);
    }
}
