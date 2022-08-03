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

use Doctrine\ORM\EntityManager;
use Knp\Menu\Factory\ExtensionInterface;
use SWP\Bundle\MenuBundle\Doctrine\MenuItemRepositoryInterface;
use SWP\Bundle\MenuBundle\Manager\MenuItemManager;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\MenuBundle\Manager\MenuItemManagerInterface;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @mixin MenuItemManager
 */
final class MenuItemManagerSpec extends ObjectBehavior
{
    public function let(MenuItemRepositoryInterface $menuItemRepository, EntityManager $objectManager, ExtensionInterface $extensionChain)
    {
        $this->beConstructedWith($menuItemRepository, $objectManager, $extensionChain);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(MenuItemManager::class);
    }

    public function it_implements_an_interface()
    {
        $this->shouldImplement(MenuItemManagerInterface::class);
    }

    public function it_moves_source_item_at_first_position_under_parent(
        MenuItemInterface $sourceItem,
        MenuItemInterface $parentItem,
        MenuItemRepositoryInterface $menuItemRepository,
        EntityManager $objectManager
    ) {
        $sourceItem->getPosition()->willReturn(2);
        $menuItemRepository->persistAsFirstChildOf($sourceItem, $parentItem)->shouldBeCalled();
        $sourceItem->setPosition(0)->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $this->move($sourceItem, $parentItem, 0);
    }

    public function it_moves_source_item_in_the_middle_of_subtree_from_last_position(
        MenuItemInterface $sourceItem,
        MenuItemInterface $parentItem,
        MenuItemInterface $afterItem,
        MenuItemRepositoryInterface $menuItemRepository,
        EntityManager $objectManager
    ) {
        $sourceItem->getPosition()->willReturn(2);
        $menuItemRepository->findChildByParentAndPosition($parentItem, 0)->willReturn($afterItem);
        $menuItemRepository->persistAsNextSiblingOf($sourceItem, $afterItem)->shouldBeCalled();
        $sourceItem->getPosition()->shouldBeCalled();
        $sourceItem->setPosition(1)->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $this->move($sourceItem, $parentItem, 1);
    }

    public function it_moves_source_item_at_last_position_in_subtree(
        MenuItemInterface $sourceItem,
        MenuItemInterface $parentItem,
        MenuItemInterface $afterItem,
        MenuItemRepositoryInterface $menuItemRepository,
        EntityManager $objectManager
    ) {
        $sourceItem->getPosition()->willReturn(1);
        $menuItemRepository->findChildByParentAndPosition($parentItem, 2)->willReturn($afterItem);
        $menuItemRepository->persistAsNextSiblingOf($sourceItem, $afterItem)->shouldBeCalled();
        $sourceItem->getPosition()->shouldBeCalled();
        $sourceItem->setPosition(2)->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $this->move($sourceItem, $parentItem, 2);
    }

    public function it_throws_exception_when_item_is_already_at_first_position(
        MenuItemInterface $sourceItem,
        MenuItemInterface $parentItem,
        MenuItemRepositoryInterface $menuItemRepository,
        EntityManager $objectManager
    ) {
        $sourceItem->getPosition()->willReturn(0);
        $sourceItem->getId()->willReturn(5);
        $sourceItem->getParent()->willReturn($parentItem);
        $menuItemRepository->persistAsFirstChildOf($sourceItem, $parentItem)->shouldNotBeCalled();
        $sourceItem->setPosition(0)->shouldNotBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $this->shouldThrow(ConflictHttpException::class)
            ->duringMove($sourceItem, $parentItem, 0);
    }

    public function it_throws_exception_when_item_is_already_at_the_same_position_in_the_middle_of_subtree(
        MenuItemInterface $sourceItem,
        MenuItemInterface $parentItem,
        MenuItemInterface $afterItem,
        MenuItemRepositoryInterface $menuItemRepository,
        EntityManager $objectManager
    ) {
        $sourceItem->getPosition()->willReturn(2);
        $sourceItem->getId()->shouldBeCalled();
        $sourceItem->getParent()->willReturn($parentItem);
        $menuItemRepository->findChildByParentAndPosition($parentItem, 2)->shouldNotBeCalled();
        $menuItemRepository->persistAsNextSiblingOf($sourceItem, $afterItem)->shouldNotBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $this->shouldThrow(ConflictHttpException::class)
            ->duringMove($sourceItem, $parentItem, 2);
    }

    public function it_throws_exception_if_item_after_moved_item_should_be_placed_does_not_exist(
        MenuItemInterface $sourceItem,
        MenuItemInterface $parentItem,
        MenuItemInterface $afterItem,
        MenuItemRepositoryInterface $menuItemRepository,
        EntityManager $objectManager
    ) {
        $sourceItem->getPosition()->willReturn(1);
        $menuItemRepository->findChildByParentAndPosition($parentItem, 2)->willReturn(null);
        $menuItemRepository->persistAsNextSiblingOf($sourceItem, $afterItem)->shouldNotBeCalled();
        $sourceItem->getPosition()->shouldBeCalled();
        $sourceItem->setPosition(2)->shouldNotBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $this->shouldThrow(HttpException::class)
            ->duringMove($sourceItem, $parentItem, 2);
    }

    public function it_updates_menu_options(
        MenuItemInterface $menu,
        ExtensionInterface $extensionChain
    ) {
        $menu->getLabel()->willReturn('testlabel');
        $menu->getUri()->willReturn('/test');
        $options = [
            'uri' => '/test',
            'label' => 'testlabel',
        ];
        $extensionChain->buildOptions($options)->shouldBeCalled()->willReturn($options);
        $extensionChain->buildItem($menu, $options)->shouldBeCalled();
        $this->update($menu);
    }
}
