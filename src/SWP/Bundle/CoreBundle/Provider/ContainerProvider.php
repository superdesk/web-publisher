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

use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use SWP\Component\Revision\Context\RevisionContext;
use SWP\Bundle\TemplatesSystemBundle\Provider\ContainerProvider as BaseContentProvider;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Revision\Model\RevisionInterface;
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
        /** @var PersistentCollection $containers */
        $containers = $this->addRevisionToQueryBuilder($this->containerRepository->getByName($name))
            ->getQuery()
            ->getResult();

        return $this->getContainerFromCollection($containers);
    }

    /**
     * {@inheritdoc}
     */
    public function getOneById($uuid)
    {
        $qb = $this->containerRepository->createQueryBuilder('c')
            ->andWhere('c.uuid = :uuid')
            ->setParameters([
                'uuid' => $uuid,
            ]);

        $containers = $this->addRevisionToQueryBuilder($qb)
            ->getQuery()
            ->getResult();

        return $this->getContainerFromCollection($containers);
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

    private function addRevisionToQueryBuilder(QueryBuilder $queryBuilder)
    {
        if (RevisionInterface::STATE_NEW === $this->revisionContext->getCurrentRevision()->getStatus()) {
            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq('c.revision', $this->revisionContext->getPublishedRevision()->getId()),
                $queryBuilder->expr()->eq('c.revision', $this->revisionContext->getCurrentRevision()->getId())
            ));
        } else {
            $queryBuilder
                ->andWhere('c.revision = :currentRevision')
                ->setParameter('currentRevision', $this->revisionContext->getCurrentRevision());
        }

        return $queryBuilder;
    }

    private function getContainerFromCollection($containers)
    {
        if (empty($containers)) {
            return;
        }

        if (count($containers) > 1) {
            /** @var \SWP\Bundle\CoreBundle\Model\ContainerInterface $container */
            foreach ($containers as $container) {
                if (RevisionInterface::STATE_NEW === $container->getRevision()->getStatus()) {
                    break;
                }
            }
        } else {
            $container = $containers[0];
        }

        return $container;
    }
}
