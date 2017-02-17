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
use SWP\Bundle\TemplatesSystemBundle\Repository\ContainerRepository;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Revision\Model\RevisionInterface;

class RevisionAwareContainerRepository extends ContainerRepository implements RevisionAwareContainerRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getIds(RevisionInterface $revision): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->select('c.uuid')
            ->where('c.revision = :revision')
            ->setParameter('revision', $revision);
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerWithoutProvidedIds(array $ids, RevisionInterface $revision): QueryBuilder
    {
        $criteria = new Criteria();
        $criteria->set('revision', $revision->getPrevious());
        $qb = $this->getQueryByCriteria($criteria, [], 'c');
        if (count($ids) > 0) {
            $qb->andWhere('c.uuid NOT IN (:ids)')->setParameter('ids', $ids);
        }

        return $qb;
    }
}
