<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;

class ApiKeyRepository extends EntityRepository implements ApiKeyRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getValidToken(string $token): QueryBuilder
    {
        $qb = $this->getQueryByCriteria(new Criteria(['apiKey' => $token]), [], 'ak')
            ->leftJoin('ak.user', 'u')
            ->addSelect('u')
            ->andWhere('ak.validTo >= :now')
            ->setParameter('now', new \DateTime());

        return $qb;
    }
}
