<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;

/**
 * Container Repository.
 */
class ContainerRepository extends EntityRepository implements ContainerRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getByName(string $name): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.name = :name')
            ->setParameters([
                'name' => $name,
            ]);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameters([
                'id' => $id,
            ]);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getHttpCacheCheckQuery($id): Query
    {
        $query = $this->getEntityManager()->createQuery("select partial c.{id,createdAt,updatedAt} from SWP\TemplatesSystemBundle\Model\Container c WHERE c.id = :id");
        $query->setParameters([
            'id' => $id,
        ]);

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(): Query
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->getQuery();
    }
}
