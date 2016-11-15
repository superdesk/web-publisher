<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Menu Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MenuBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\MenuBundle\Doctrine\MenuItemRepositoryInterface;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;

class MenuItemManager implements MenuItemManagerInterface
{
    /**
     * @var MenuItemRepositoryInterface
     */
    protected $menuItemRepository;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * MenuItemManager constructor.
     *
     * @param MenuItemRepositoryInterface $menuItemRepository
     * @param ObjectManager               $objectManager
     */
    public function __construct(MenuItemRepositoryInterface $menuItemRepository, ObjectManager $objectManager)
    {
        $this->menuItemRepository = $menuItemRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function moveToParent(
        MenuItemInterface $sourceItem,
        MenuItemInterface $parent
    ) {
        $this->menuItemRepository->persistAsFirstChildOf($sourceItem, $parent);

        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function moveAfter(
        MenuItemInterface $sourceItem,
        MenuItemInterface $afterItem
    ) {
        $this->menuItemRepository->persistAsNextSiblingOf($sourceItem, $afterItem);

        $this->objectManager->flush();
    }
}
