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

namespace SWP\Bundle\StorageBundle\Doctrine\ODM\PHPCR;

use Doctrine\ODM\PHPCR\DocumentRepository as BaseDocumentRepository;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Storage\Model\PersistableInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DocumentRepository extends BaseDocumentRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function add(PersistableInterface $object)
    {
        $this->dm->persist($object);
        $this->dm->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(PersistableInterface $object)
    {
        if (null !== $this->find($object->getId())) {
            $this->dm->remove($object);
            $this->dm->flush($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function persist(PersistableInterface $object)
    {
        $this->dm->persist($object);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->dm->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginatedByCriteria(
        EventDispatcherInterface $eventDispatcher,
        Criteria $criteria,
        array $sorting = [],
        PaginationData $paginationData = null
    ) {
        throw new \Exception('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryByCriteria(Criteria $criteria, array $sorting, string $alias): QueryBuilder
    {
        throw new \Exception('Not implemented');
    }
}
