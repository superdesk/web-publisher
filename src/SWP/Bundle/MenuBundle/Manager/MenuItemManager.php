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
use Knp\Menu\Factory\ExtensionInterface;
use SWP\Bundle\MenuBundle\Doctrine\MenuItemRepositoryInterface;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class MenuItemManager implements MenuItemManagerInterface
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
     * @var ExtensionInterface
     */
    protected $extensionsChain;

    /**
     * MenuItemManager constructor.
     *
     * @param MenuItemRepositoryInterface $menuItemRepository
     * @param ObjectManager               $objectManager
     */
    public function __construct(
        MenuItemRepositoryInterface $menuItemRepository,
        ObjectManager $objectManager,
        ExtensionInterface $extensionsChain
    ) {
        $this->menuItemRepository = $menuItemRepository;
        $this->objectManager = $objectManager;
        $this->extensionsChain = $extensionsChain;
    }

    /**
     * {@inheritdoc}
     */
    public function move(MenuItemInterface $sourceItem, MenuItemInterface $parent, int $position = 0)
    {
        if (0 === $position) {
            $this->ensurePositionIsValid($sourceItem, $position);
            $this->menuItemRepository->persistAsFirstChildOf($sourceItem, $parent);
        } else {
            $afterItemPosition = $position;
            // when moving item from last to middle position
            if ($afterItemPosition < $sourceItem->getPosition()) {
                $afterItemPosition -= 1;
            }

            $this->ensurePositionIsValid($sourceItem, $afterItemPosition);
            // find menu item after which source item should be placed
            $afterItem = $this->menuItemRepository->findChildByParentAndPosition($parent, $afterItemPosition);

            if (null === $afterItem) {
                throw new HttpException(400, sprintf(
                    'You can not insert menu item at position %d. Position is not valid.',
                    $position
                ));
            }

            $this->menuItemRepository->persistAsNextSiblingOf($sourceItem, $afterItem);
        }

        $sourceItem->setPosition($position);
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function update(MenuItemInterface $menu)
    {
        if (null !== $menu->getRoute()) {
            // Make copy of label as it's cleared by one of extensions
            $label = $menu->getLabel();
            $options = $this->extensionsChain->buildOptions(
                [
                    'route' => $menu->getRoute() ? $menu->getRoute()->getName() : null,
                ]
            );
            $this->extensionsChain->buildItem($menu, $options);
            $menu->setLabel($label);
        }
    }

    private function ensurePositionIsValid(MenuItemInterface $menuItem, int $position)
    {
        if ($menuItem->getPosition() === $position) {
            throw new ConflictHttpException(sprintf(
                'Menu item %d is already placed at position %d.',
                $menuItem->getId(),
                $position
            ));
        }
    }
}
