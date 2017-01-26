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
use SWP\Bundle\CoreBundle\EventListener\MenuWidgetDeleteListener;
use SWP\Bundle\CoreBundle\Model\WidgetModelInterface;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;
use SWP\Component\Common\Exception\UnexpectedTypeException;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class MenuWidgetDeleteListenerSpec extends ObjectBehavior
{
    public function let(RepositoryInterface $widgetRepository)
    {
        $this->beConstructedWith($widgetRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(MenuWidgetDeleteListener::class);
    }

    public function it_throws_exception(
        GenericEvent $event,
        \stdClass $menuItem
    ) {
        $event->getSubject()->willReturn($menuItem);

        $this->shouldThrow(UnexpectedTypeException::class)
            ->duringOnMenuDeleted($event);
    }

    public function it_deletes_menu_widget_on_menu_deleted_event(
        GenericEvent $event,
        MenuItemInterface $menuItem,
        RepositoryInterface $widgetRepository,
        WidgetModelInterface $widget
    ) {
        $menuItem->getName()->willReturn('menuNav');
        $menuItem->getParent()->willReturn(null);
        $event->getSubject()->willReturn($menuItem);

        $widgetRepository->findOneBy(['name' => 'menuNav'])->willReturn($widget);
        $widgetRepository->remove($widget)->shouldBeCalled();

        $this->onMenuDeleted($event);
    }

    public function it_deletes_menu_widget_only_for_root_menus(
        GenericEvent $event,
        MenuItemInterface $menuItem,
        RepositoryInterface $widgetRepository,
        WidgetModelInterface $widget
    ) {
        $menuItem->getName()->willReturn('menuNav');
        $menuItem->getParent()->willReturn(1);
        $event->getSubject()->willReturn($menuItem);

        $widgetRepository->findOneBy(['name' => 'menuNav'])->shouldNotBeCalled();
        $widgetRepository->remove($widget)->shouldNotBeCalled();

        $this->onMenuDeleted($event);
    }

    public function it_does_nothing_when_widget_does_not_exist(
        GenericEvent $event,
        MenuItemInterface $menuItem,
        RepositoryInterface $widgetRepository,
        WidgetModelInterface $widget
    ) {
        $menuItem->getName()->willReturn('menuNav');
        $menuItem->getParent()->willReturn(1);
        $event->getSubject()->willReturn($menuItem);

        $widgetRepository->findOneBy(['name' => 'menuNav'])->willReturn(null);
        $widgetRepository->remove($widget)->shouldNotBeCalled();

        $this->onMenuDeleted($event);
    }
}
