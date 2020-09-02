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

namespace SWP\Bundle\MenuBundle\Doctrine;

use Knp\Component\Pager\Pagination\PaginationInterface;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

interface MenuItemRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $name
     *
     * @return MenuItemInterface|null
     */
    public function getOneMenuItemByName(string $name);

    /**
     * @param int $id
     *
     * @return MenuItemInterface|null
     */
    public function getOneMenuItemById(int $id);

    /**
     * @param MenuItemInterface $menuItem
     *
     * @return PaginationInterface
     */
    public function findChildrenAsTree(MenuItemInterface $menuItem);

    public function findRootNodes(int $page = 1, int $limit = 10);

    /**
     * @param MenuItemInterface $node
     * @param MenuItemInterface $parent
     */
    public function persistAsFirstChildOf(MenuItemInterface $node, MenuItemInterface $parent);

    /**
     * @param MenuItemInterface $node
     * @param MenuItemInterface $sibling
     */
    public function persistAsNextSiblingOf(MenuItemInterface $node, MenuItemInterface $sibling);

    /**
     * @param MenuItemInterface $parent
     * @param int               $position
     *
     * @return mixed
     */
    public function findChildByParentAndPosition(MenuItemInterface $parent, int $position);
}
