<?php


/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\TemplateEngineBundle\Repository;

/**
 * Container Repository.
 */
class ContainerRepository extends \Doctrine\ORM\EntityRepository
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
     * Get Query for all Containers
     *
     * @return \Doctrine\ORM\Query
     */
    public function getAll()
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->getQuery();
    }
}
