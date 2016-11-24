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

use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\Pagination\PaginationInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Storage\Model\PersistableInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class EntityRepository extends BaseEntityRepository implements RepositoryInterface
{
    /**
     * Default value for number of results.
     */
    const MAX_RESULTS = 10;

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
    public function getPaginatedByCriteria(Criteria $criteria, array $sorting = [], PaginationData $paginationData = null)
    {
        $queryBuilder = $this->getQueryByCriteria($criteria, $sorting, 's');

        if (null === $paginationData) {
            $paginationData = new PaginationData();
        }

        return $this->getPaginator($queryBuilder, $paginationData);
    }

    public function getQueryByCriteria(Criteria $criteria, array $sorting, string $alias): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder($alias);
        $this->applyCriteria($queryBuilder, $criteria, $alias);
        $this->applySorting($queryBuilder, $sorting, $alias);
        $this->applyLimiting($queryBuilder, $criteria);

        return $queryBuilder;
    }

    /**
     * @param $queryBuilder
     * @param PaginationData $paginationData
     *
     * @return PaginationInterface
     */
    protected function getPaginator($queryBuilder, PaginationData $paginationData)
    {
        $paginator = new Paginator();

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
            if (!in_array($property, $properties)) {
                continue;
            }

            $name = $this->getPropertyName($property, $alias);
            if (null === $value) {
                $queryBuilder->andWhere($queryBuilder->expr()->isNull($name));
            } elseif (is_array($value)) {
                $queryBuilder->andWhere($queryBuilder->expr()->in($name, $value));
            } elseif ('' !== $value) {
                $parameter = str_replace('.', '_', $property);
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->eq($name, ':'.$parameter))
                    ->setParameter($parameter, $value)
                ;
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $sorting
     * @param string       $alias
     */
    protected function applySorting(QueryBuilder $queryBuilder, array $sorting, string $alias)
    {
        foreach ($sorting as $property => $order) {
            if (!empty($order)) {
                $queryBuilder->addOrderBy($this->getPropertyName($property, $alias), $order);
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Criteria     $criteria
     */
    public function applyLimiting(QueryBuilder $queryBuilder, Criteria $criteria)
    {
        $queryBuilder->setFirstResult($criteria->get('firstResult', 0));
        if ($criteria->has('maxResults')) {
            $queryBuilder->setMaxResults($criteria->get('maxResults'));
        } else {
            $queryBuilder->setMaxResults(self::MAX_RESULTS);
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
        if (false === strpos($name, '.')) {
            return $alias.'.'.$name;
        }

        return $name;
    }
}
