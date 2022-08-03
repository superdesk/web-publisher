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

use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\Factory\ExtensionInterface;
use SWP\Bundle\MenuBundle\Doctrine\MenuItemRepositoryInterface;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MenuItemManager implements MenuItemManagerInterface
{
    /**
     * @var MenuItemRepositoryInterface
     */
    protected $menuItemRepository;

    /**
     * @var EntityManagerInterface
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
     * @param EntityManagerInterface               $objectManager
     * @param ExtensionInterface          $extensionsChain
     */
    public function __construct(MenuItemRepositoryInterface $menuItemRepository, EntityManagerInterface $objectManager, ExtensionInterface $extensionsChain)
    {
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
            $this->ensurePositionIsValid($sourceItem, $position, $parent);
            $this->menuItemRepository->persistAsFirstChildOf($sourceItem, $parent);
        } else {
            $afterItemPosition = $position;
            // when moving item from last to middle position
            if ($afterItemPosition < $sourceItem->getPosition()) {
                $afterItemPosition -= 1;
            }

            $this->ensurePositionIsValid($sourceItem, $afterItemPosition, $parent);
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
        $this->updateOptions($menu);
    }

    /**
     * @param MenuItemInterface $menu
     * @param array             $options
     */
    protected function updateOptions(MenuItemInterface $menu, array $options = [])
    {
        $options = array_merge($options, [
            'uri' => $menu->getUri(),
            'label' => $menu->getLabel(),
        ]);

        $options = $this->extensionsChain->buildOptions($options);
        $this->extensionsChain->buildItem($menu, $options);
    }

    /**
     * @param MenuItemInterface $menuItem
     * @param int               $position
     * @param MenuItemInterface $parent
     */
    private function ensurePositionIsValid(MenuItemInterface $menuItem, int $position, MenuItemInterface $parent)
    {
        if ($menuItem->getPosition() === $position && $menuItem->getParent() === $parent) {
            throw new ConflictHttpException(sprintf(
                'Menu item %d is already placed at position %d.',
                $menuItem->getId(),
                $position
            ));
        }
    }
}
