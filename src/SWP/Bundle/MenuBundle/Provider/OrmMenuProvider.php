<?php

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
        $menuItem = $this->repository->getOneMenuItemByName($name);

        return $menuItem instanceof MenuItemInterface;
    }
}
