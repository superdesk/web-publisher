<?php


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

use Doctrine\ORM\EntityRepository;

/**
 * Container Repository.
 */
class ContainerRepository extends EntityRepository
{
    /**
     * Get Query for Container searched by name.
     *
     * @param string $name
     *
     * @return \Doctrine\ORM\Query
     */
    public function getByName($name)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.name = :name')
            ->setParameters([
                'name' => $name,
            ]);

        return $qb->getQuery();
    }

    /**
     * Get Query for Container searched by id.
     *
     * @param string $id
     *
     * @return \Doctrine\ORM\Query
     */
    public function getById($id)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameters([
                'id' => $id,
            ]);

        return $qb->getQuery();
    }

    /**
     * Get Query for Container searched by id but only with id, createdAt and updatedAt fields.
     *
     * @param string $id
     *
     * @return \Doctrine\ORM\Query
     */
    public function getHttpCacheCheckQuery($id)
    {
        $query = $this->getEntityManager()->createQuery("select partial c.{id,createdAt,updatedAt} from SWP\TemplatesSystemBundle\Model\Container c WHERE c.id = :id");
        $query->setParameters([
            'id' => $id,
        ]);

        return $query;
    }

    /**
     * Get Query for all Containers.
     *
     * @return \Doctrine\ORM\Query
     */
    public function getAll()
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->getQuery();
    }
}
