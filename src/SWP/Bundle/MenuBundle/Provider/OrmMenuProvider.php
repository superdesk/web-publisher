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

namespace SWP\Bundle\MenuBundle\Provider;

use Knp\Menu\Provider\MenuProviderInterface;
use SWP\Bundle\MenuBundle\Doctrine\MenuItemRepositoryInterface;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;

final class OrmMenuProvider implements MenuProviderInterface
{
    /**
     * @var MenuItemRepositoryInterface
     */
    private $repository;

    /**
     * MenuProvider constructor.
     *
     * @param MenuItemRepositoryInterface $repository
     */
    public function __construct(MenuItemRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, array $options = [])
    {
        $menuItem = $this->repository->getOneMenuItemByName($name);

        if (!$menuItem instanceof MenuItemInterface) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        return $menuItem;
    }

    /**
     * {@inheritdoc}
     */
    public function has($name, array $options = [])
    {
        if (null === $name) {
            return false;
        }

        $menuItem = $this->repository->getOneMenuItemByName($name);

        return $menuItem instanceof MenuItemInterface;
    }
}
