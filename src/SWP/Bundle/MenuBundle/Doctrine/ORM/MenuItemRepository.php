<?php

declare(strict_types=1);

namespace SWP\Bundle\MenuBundle\Doctrine\ORM;

use SWP\Bundle\MenuBundle\Doctrine\MenuItemRepositoryInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;

class MenuItemRepository extends EntityRepository implements MenuItemRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOneMenuItemByName(string $name)
    {
        return $this->createQueryBuilder('m')
            ->where('m.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getOneMenuItemById(int $id)
    {
        return $this->createQueryBuilder('m')
            ->where('m.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
