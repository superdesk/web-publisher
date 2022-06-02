<?php

/*
 * This file is part of the Superdesk Web Publisher Storage Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\StorageBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\Pagination\PaginationInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Storage\Model\PersistableInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

trait EntityRepositoryTrait
{
    /**
     * {@inheritdoc}
     */
    public function persist(PersistableInterface $object)
    {
        $this->_em->persist($object);
    }

    /**
     * {@inheritdoc}
     */
    public function add(PersistableInterface $object)
    {
        $this->_em->persist($object);
        $this->_em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->_em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(PersistableInterface $object)
    {
        if (null !== $this->find($object->getId())) {
            $this->_em->remove($object);
            $this->_em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginatedByCriteria(EventDispatcherInterface $eventDispatcher, Criteria $criteria, array $sorting = [], PaginationData $paginationData = null)
    {
        $queryBuilder = $this->getQueryByCriteria($criteria, $sorting, 's');

        if (null === $paginationData) {
            $paginationData = new PaginationData();
        }

        return $this->getPaginator($eventDispatcher, $queryBuilder, $paginationData);
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryByCriteria(Criteria $criteria, array $sorting, string $alias): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder($alias);
        $this->applyCriteria($queryBuilder, $criteria, $alias);
        $this->applySorting($queryBuilder, $sorting, $alias, $criteria);
        $this->applyLimiting($queryBuilder, $criteria);

        return $queryBuilder;
    }

    public function applyLimiting(QueryBuilder $queryBuilder, Criteria $criteria): void
    {
        if (!is_numeric($firstResult = $criteria->get('firstResult', 0))) {
            $firstResult = 0;
        }
        $queryBuilder->setFirstResult(abs($firstResult));

        if ($criteria->has('maxResults') && is_numeric($criteria->get('maxResults'))) {
            $queryBuilder->setMaxResults(abs($criteria->get('maxResults')));
        } else {
            $queryBuilder->setMaxResults(self::MAX_RESULTS);
        }
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param QueryBuilder   $queryBuilder
     * @param PaginationData $paginationData
     *
     * @return PaginationInterface
     */
    protected function getPaginator(EventDispatcherInterface $eventDispatcher, QueryBuilder $queryBuilder, PaginationData $paginationData)
    {
        $paginator = new Paginator($eventDispatcher);

        return $paginator->paginate($queryBuilder, $paginationData->getPageNumber(), $paginationData->getLimit());
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Criteria     $criteria
     * @param string       $alias
     */
    protected function applyCriteria(QueryBuilder $queryBuilder, Criteria $criteria, string $alias)
    {
        $properties = array_merge($this->getClassMetadata()->getFieldNames(), $this->getClassMetadata()->getAssociationNames());
        foreach ($criteria->all() as $property => $value) {
            if (!\in_array($property, $properties)) {
                continue;
            }

            $name = $this->getPropertyName($property, $alias);
            if (null === $value) {
                $queryBuilder->andWhere($queryBuilder->expr()->isNull($name));
            } elseif (\is_array($value)) {
                $queryBuilder->andWhere($queryBuilder->expr()->in($name, $value));
            } elseif ('' !== $value) {
                $parameter = \str_replace('.', '_', $property);
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->eq($name, ':'.$parameter))
                    ->setParameter($parameter, $value)
                ;
            }
        }
    }

    /**
     * @param QueryBuilder  $queryBuilder
     * @param array         $sorting
     * @param string        $alias
     * @param Criteria|null $criteria
     */
    protected function applySorting(QueryBuilder $queryBuilder, array $sorting, string $alias, Criteria $criteria = null)
    {
        $properties = \array_merge($this->getClassMetadata()->getFieldNames(), $this->getClassMetadata()->getAssociationNames());
        foreach ($sorting as $property => $order) {
            if (!\in_array($property, $properties)) {
                continue;
            }

            if (!empty($order)) {
                $queryBuilder->addOrderBy($this->getPropertyName($property, $alias), $order);
                unset($sorting[$property]);
            }
        }
    }

    /**
     * @param string $name
     * @param string $alias
     *
     * @return string
     */
    protected function getPropertyName(string $name, string $alias)
    {
        if (false === \strpos($name, '.')) {
            return $alias.'.'.$name;
        }

        return $name;
    }
}
