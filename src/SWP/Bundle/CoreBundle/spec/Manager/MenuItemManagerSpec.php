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

namespace spec\SWP\Bundle\CoreBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\Factory\ExtensionInterface;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Manager\MenuItemManager;
use SWP\Bundle\CoreBundle\Model\MenuItemInterface;
use SWP\Bundle\MenuBundle\Doctrine\MenuItemRepositoryInterface;
use SWP\Bundle\MenuBundle\Manager\MenuItemManagerInterface;

final class MenuItemManagerSpec extends ObjectBehavior
{
    public function let(
        MenuItemRepositoryInterface $menuItemRepository,
        ObjectManager $objectManager,
        ExtensionInterface $extensionChain
    ) {
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

    public function it_updates_menu_options(
        MenuItemInterface $menu,
        ExtensionInterface $extensionChain,
        RouteInterface $route
    ) {
        $route->getName()->willReturn('sports');

        $menu->getLabel()->willReturn('testlabel');
        $menu->getUri()->willReturn('/test');
        $menu->getRoute()->willReturn($route);
        $options = [
            'uri' => '/test',
            'label' => 'testlabel',
            'route' => 'sports',
        ];
        $extensionChain->buildOptions($options)->shouldBeCalled()->willReturn($options);
        $extensionChain->buildItem($menu, $options)->shouldBeCalled();
        $this->update($menu);
    }

    public function it_updates_menu_options_when_route_empty(
        MenuItemInterface $menu,
        ExtensionInterface $extensionChain
    ) {
        $menu->getLabel()->willReturn('testlabel');
        $menu->getUri()->willReturn('/test');
        $menu->getRoute()->willReturn(null);
        $options = [
            'uri' => '/test',
            'label' => 'testlabel',
            'route' => null,
        ];
        $extensionChain->buildOptions($options)->shouldBeCalled()->willReturn($options);
        $extensionChain->buildItem($menu, $options)->shouldBeCalled();
        $this->update($menu);
    }
}
