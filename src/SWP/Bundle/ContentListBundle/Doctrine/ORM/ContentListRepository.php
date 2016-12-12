<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentListBundle\Doctrine\ORM;

use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;

class ContentListRepository extends EntityRepository implements ContentListRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('cl')
            ->where('cl.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->_em->flush();
    }
}
