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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Gedmo\Exception\UnexpectedValueException;
use Gedmo\Tool\Wrapper\EntityWrapper;
use Gedmo\Tree\TreeListener;
use SWP\Bundle\MenuBundle\Doctrine\MenuItemRepositoryInterface;
use SWP\Bundle\MenuBundle\Model\MenuItemInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Common\Pagination\PaginationData;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MenuItemRepository extends EntityRepository implements MenuItemRepositoryInterface
{
    /**
     * Tree listener on event manager.
     *
     * @var TreeListener
     */
    protected $treeListener;

    /**
     * MenuItemRepository constructor.
     */
    public function __construct(EntityManager $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $treeListener = null;
        foreach ($em->getEventManager()->getListeners() as $listeners) {
            foreach ($listeners as $listener) {
                if ($listener instanceof TreeListener) {
                    $treeListener = $listener;

                    break;
                }
            }
            if ($treeListener) {
                break;
            }
        }

        if (is_null($treeListener)) {
            throw new \Gedmo\Exception\InvalidMappingException('Tree listener was not found on your entity manager, it must be hooked into the event manager');
        }

        $this->treeListener = $treeListener;
    }

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
     * {@inheritdoc}
     */
    public function persistAsFirstChildOf(MenuItemInterface $node, MenuItemInterface $parent)
    {
        $wrapped = new EntityWrapper($node, $this->_em);
        $meta = $this->getClassMetadata();
        $config = $this->treeListener->getConfiguration($this->_em, $meta->name);

        $wrapped->setPropertyValue($config['parent'], $parent);

        $wrapped->setPropertyValue($config['left'], 0);
        $oid = spl_object_hash($node);
        $this->treeListener
            ->getStrategy($this->_em, $meta->name)
            ->setNodePosition($oid, 'FirstChild')
        ;

        $this->_em->persist($node);
    }

    /**
     * {@inheritdoc}
     */
    public function persistAsNextSiblingOf(MenuItemInterface $node, MenuItemInterface $sibling)
    {
        $wrapped = new EntityWrapper($node, $this->_em);
        $meta = $this->getClassMetadata();
        $config = $this->treeListener->getConfiguration($this->_em, $meta->name);

        $wrappedSibling = new EntityWrapper($sibling, $this->_em);
        $newParent = $wrappedSibling->getPropertyValue($config['parent']);
        if (null === $newParent && isset($config['root'])) {
            throw new UnexpectedValueException('Cannot persist sibling for a root node, tree operation is not possible');
        }

        $node->sibling = $sibling;
        $sibling = $newParent;

        $wrapped->setPropertyValue($config['parent'], $sibling);
        $wrapped->setPropertyValue($config['left'], 0);
        $oid = spl_object_hash($node);
        $this->treeListener
            ->getStrategy($this->_em, $meta->name)
            ->setNodePosition($oid, 'NextSibling')
        ;

        $this->_em->persist($node);

        return $this;
    }
}
