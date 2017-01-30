<?php

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

namespace SWP\Bundle\CoreBundle\Provider;

use Doctrine\ORM\Query;
use SWP\Component\Revision\Context\RevisionContext;
use SWP\Bundle\TemplatesSystemBundle\Provider\ContainerProvider as BaseContentProvider;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;

/**
 * Revisions Aware ContainerProvider.
 */
class ContainerProvider extends BaseContentProvider
{
    /**
     * @var RevisionContext
     */
    protected $revisionContext;

    /**
     * {@inheritdoc}
     */
    public function getQueryForAll(): Query
    {
        $criteria = new Criteria();
        $criteria->set('revision', $this->revisionContext->getCurrentRevision());

        return $this->containerRepository->getQueryByCriteria($criteria, [], 'r')->getQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function getOneByName(string $name)
    {
        $qb = $this->containerRepository->getByName($name);
        $qb->andWhere('c.revision = :revision')
            ->setParameter('revision', $this->revisionContext->getCurrentRevision());

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getOneById($uuid)
    {
        $qb = $this->containerRepository->createQueryBuilder('c')
            ->andWhere('c.revision = :revision')
            ->andWhere('c.uuid = :uuid')
            ->setParameters([
                'revision' => $this->revisionContext->getCurrentRevision(),
                'uuid' => $uuid,
            ]);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerWidgets(ContainerInterface $container): array
    {
        return $this->containerWidgetRepository
            ->getSortedWidgets(['container' => $container])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param RevisionContext $revisionContext
     */
    public function setRevisionContext(RevisionContext $revisionContext)
    {
        $this->revisionContext = $revisionContext;
    }
}
