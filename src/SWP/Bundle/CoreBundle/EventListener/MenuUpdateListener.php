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

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\RouteEvent;
use SWP\Bundle\MenuBundle\Doctrine\MenuItemRepositoryInterface;
use SWP\Bundle\MenuBundle\Manager\MenuItemManagerInterface;
use SWP\Component\Common\Criteria\Criteria;
use Symfony\Component\EventDispatcher\GenericEvent;

final class MenuUpdateListener
{
    /**
     * @var MenuItemManagerInterface
     */
    protected $menuItemManager;

    /**
     * @var MenuItemRepositoryInterface
     */
    protected $menuItemRepository;

    public function __construct(
        MenuItemManagerInterface $menuItemManager,
        MenuItemRepositoryInterface $menuItemRepository
    ) {
        $this->menuItemManager = $menuItemManager;
        $this->menuItemRepository = $menuItemRepository;
    }

    /**
     * @param GenericEvent $event
     */
    public function onRouteUpdate(RouteEvent $event)
    {
        $this->menuItemRepository->flush();
        $criteria = new Criteria();
        $criteria->set('route', $event->getRoute());
        $menuItems = $this->menuItemRepository->getQueryByCriteria($criteria, [], 'm')->getQuery()->getResult();

        foreach ($menuItems as $menu) {
            $this->menuItemManager->update($menu);
        }
        $this->menuItemRepository->flush();
    }
}
