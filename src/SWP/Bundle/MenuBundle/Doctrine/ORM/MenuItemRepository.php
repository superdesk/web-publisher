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

namespace SWP\Bundle\MenuBundle\Doctrine\ORM;

use Gedmo\Exception\UnexpectedValueException;
use Gedmo\Tool\Wrapper\EntityWrapper;
use SWP\Bundle\MenuBundle\Doctrine\MenuItemRepositoryInterface;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\NestedTreeEntityRepository;
use SWP\Component\Common\Pagination\PaginationData;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MenuItemRepository extends NestedTreeEntityRepository implements MenuItemRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOneMenuItemByName(string $name)
    {
        return $this->createQueryBuilder('m')
            ->addSelect('c')
            ->leftJoin('m.children', 'c')
            ->addSelect('ch')
            ->leftJoin('c.children', 'ch')
            ->addSelect('chc')
            ->leftJoin('ch.children', 'chc')
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
            ->addSelect('c')
            ->leftJoin('m.children', 'c')
            ->addSelect('ch')
            ->leftJoin('c.children', 'ch')
            ->addSelect('chc')
            ->leftJoin('ch.children', 'chc')
            ->where('m.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findChildrenAsTree(EventDispatcherInterface $eventDispatcher,MenuItemInterface $menuItem)
    {
        $queryBuilder = $this->createQueryBuilder('m');
        $queryBuilder
            ->addSelect('children')
            ->leftJoin('m.children', 'children')
            ->where('m.parent = :parent')
            ->addOrderBy('m.root')
            ->setParameter('parent', $menuItem)
            ->orderBy('m.lft', 'asc')
        ;

        return $this->getPaginator($eventDispatcher,$queryBuilder, new PaginationData());
    }

    /**
     * {@inheritdoc}
     */
    public function findRootNodes(EventDispatcherInterface $eventDispatcher, int $page = 1, int $limit = 10)
    {
        if ($page <= 0) {
            $page = 1;
        }

        if ($limit <= 0) {
            $limit = 10;
        }

        $queryBuilder = $this->createQueryBuilder('m');
        $queryBuilder
            ->addSelect('children')
            ->leftJoin('m.children', 'children')
            ->where($queryBuilder->expr()->isNull('m.parent'))
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('m.id', 'asc');

        $pagination = new PaginationData();
        $pagination->setPageNumber($page);
        $pagination->setLimit($limit);

        return $this->getPaginator($eventDispatcher, $queryBuilder, $pagination);
    }

    /**
     * {@inheritdoc}
     */
    public function findChildByParentAndPosition(MenuItemInterface $parent, int $position)
    {
        return $this->createQueryBuilder('m')
            ->addSelect('c')
            ->leftJoin('m.children', 'c')
            ->where('m.parent = :id')
            ->andWhere('m.position = :position')
            ->setParameters([
                'id' => $parent->getId(),
                'position' => $position,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }


  /**
   * @param MenuItemInterface $node
   * @param MenuItemInterface $parent
   */
  public function persistAsFirstChildOf(MenuItemInterface $node, MenuItemInterface $parent) {
    parent::persistAsFirstChildOf($node, $parent);
  }

  /**
   * @param MenuItemInterface $node
   * @param MenuItemInterface $sibling
   */
  public function persistAsNextSiblingOf(MenuItemInterface $node, MenuItemInterface $sibling) {
    parent::persistAsNextSiblingOf($node, $sibling);
  }
}
