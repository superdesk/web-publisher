<?php

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

declare(strict_types=1);

namespace spec\SWP\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\EventListener\MenuWidgetCreateListener;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;
use SWP\Component\Common\Exception\UnexpectedTypeException;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use SWP\Bundle\CoreBundle\Model\WidgetModelInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class MenuWidgetCreateListenerSpec extends ObjectBehavior
{
    public function let(FactoryInterface $widgetFactory, RepositoryInterface $widgetRepository)
    {
        $this->beConstructedWith($widgetFactory, $widgetRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(MenuWidgetCreateListener::class);
    }

    public function it_throws_exception(
        GenericEvent $event,
        \stdClass $menuItem
    ) {
        $event->getSubject()->willReturn($menuItem);

        $this->shouldThrow(UnexpectedTypeException::class)
            ->duringOnMenuCreated($event);
    }

    public function it_creates_menu_widget_on_menu_created_event(
        GenericEvent $event,
        MenuItemInterface $menuItem,
        FactoryInterface $widgetFactory,
        RepositoryInterface $widgetRepository,
        WidgetModelInterface $widget
    ) {
        $menuItem->getName()->willReturn('menuNav');
        $menuItem->getParent()->willReturn(null);
        $event->getSubject()->willReturn($menuItem);

        $widget->setType(WidgetModelInterface::TYPE_MENU)->shouldBeCalled();
        $widget->setName('menuNav')->shouldBeCalled();
        $widget->setParameters(['menu_name' => 'menuNav'])->shouldBeCalled();
        $widgetFactory->create()->willReturn($widget);

        $widgetRepository->add($widget)->shouldBeCalled();

        $this->onMenuCreated($event);
    }

    public function it_creates_menu_widget_only_for_root_menus(
        GenericEvent $event,
        MenuItemInterface $menuItem,
        FactoryInterface $widgetFactory,
        RepositoryInterface $widgetRepository,
        WidgetModelInterface $widget
    ) {
        $menuItem->getName()->willReturn('menuNav');
        $menuItem->getParent()->willReturn(2);
        $event->getSubject()->willReturn($menuItem);

        $widget->setType(WidgetModelInterface::TYPE_MENU)->shouldNotBeCalled();
        $widget->setName('menuNav')->shouldNotBeCalled();
        $widget->setParameters(['menu_name' => 'menuNav'])->shouldNotBeCalled();
        $widgetFactory->create()->shouldNotBeCalled();

        $widgetRepository->add($widget)->shouldNotBeCalled();

        $this->onMenuCreated($event);
    }
}
