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

namespace SWP\Bundle\MenuBundle\Form\DataTransformer;

use SWP\Bundle\MenuBundle\Doctrine\MenuItemRepositoryInterface;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

final class MenuItemToIdTransformer implements DataTransformerInterface
{
    /**
     * @var MenuItemRepositoryInterface
     */
    private $repository;

    public function __construct(MenuItemRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Transforms an object (menu item) to a string (id).
     *
     * @param MenuItemInterface|string $menuItem
     *
     * @return string|null
     *
     * @throws TransformationFailedException if object (menu item) is of wrong type
     */
    public function transform($menuItem)
    {
        if (null === $menuItem) {
            return;
        }

        if (!$menuItem instanceof MenuItemInterface) {
            throw new UnexpectedTypeException($menuItem, MenuItemInterface::class);
        }

        return $menuItem->getId();
    }

    /**
     * Transforms an id to an object (menu item).
     *
     * @param string $menuItemId
     *
     * @return MenuItemInterface|null
     *
     * @throws TransformationFailedException if object (menu item) is not found
     */
    public function reverseTransform($menuItemId)
    {
        if (null === $menuItemId) {
            return;
        }

        $menuItem = $this->repository->getOneMenuItemById((int) $menuItemId);

        if (null === $menuItem) {
            throw new TransformationFailedException(sprintf(
                'Menu with id "%s" does not exist!',
                $menuItem
            ));
        }

        return $menuItem;
    }
}
