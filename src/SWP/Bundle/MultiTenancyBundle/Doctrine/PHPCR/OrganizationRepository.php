<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle\Doctrine\PHPCR;

use SWP\Bundle\StorageBundle\Doctrine\ODM\PHPCR\DocumentRepository;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;

class OrganizationRepository extends DocumentRepository implements OrganizationRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByName($name)
    {
        return $this->findOneBy([
            'name' => $name,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByCode($code)
    {
        return $this->findOneBy([
            'code' => $code,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findAvailable()
    {
        return $this
            ->createQueryBuilder('t')
                ->where()
                    ->eq()
                        ->field('t.enabled')->literal(true)
                    ->end()
                ->end()
            ->getQuery()
            ->getResult();
    }
}
