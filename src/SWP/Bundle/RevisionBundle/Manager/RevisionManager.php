<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Publisher Revision Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\RevisionBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\RevisionBundle\Event\RevisionPublishedEvent;
use SWP\Bundle\RevisionBundle\Events;
use SWP\Component\Revision\Manager\RevisionManagerInterface;
use SWP\Component\Revision\Context\RevisionContextInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Revision\Model\RevisionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RevisionManager implements RevisionManagerInterface
{
    /**
     * @var FactoryInterface
     */
    protected $revisionFactory;

    /**
     * @var RevisionContextInterface
     */
    protected $revisionContext;

    /**
     * @var EntityManagerInterface
     */
    protected $objectManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * RevisionManager constructor.
     *
     * @param FactoryInterface         $revisionFactory
     * @param RevisionContextInterface $revisionContext
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntityManagerInterface   $objectManager
     */
    public function __construct(
        FactoryInterface $revisionFactory,
        RevisionContextInterface $revisionContext,
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $objectManager
    ) {
        $this->revisionFactory = $revisionFactory;
        $this->revisionContext = $revisionContext;
        $this->eventDispatcher = $eventDispatcher;
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(RevisionInterface $revision, RevisionInterface $workingRevision = null): RevisionInterface
    {
        if ($revision->getStatus() !== RevisionInterface::STATE_NEW) {
            return $revision;
        }

        $revision->setStatus(RevisionInterface::STATE_PUBLISHED);
        if (null !== $previousRevision = $revision->getPrevious()) {
            $previousRevision->setStatus(RevisionInterface::STATE_REPLACED);
        }
        $revision->setPublishedAt(new \DateTime());

        if (null === $workingRevision) {
            $workingRevision = $this->create($revision);
        }

        $this->revisionContext->setWorkingRevision($workingRevision);
        $this->revisionContext->setPublishedRevision($revision);
        $this->objectManager->flush();

        $this->eventDispatcher->dispatch(Events::REVISION_PUBLISH, new RevisionPublishedEvent($revision));

        return $revision;
    }

    /**
     * {@inheritdoc}
     */
    public function rollback(RevisionInterface $revision): RevisionInterface
    {
        // TODO: Implement rollback() method.
    }

    /**
     * {@inheritdoc}
     */
    public function create(RevisionInterface $previous = null): RevisionInterface
    {
        /** @var RevisionInterface $revision */
        $revision = $this->revisionFactory->create();

        if (null !== $previous) {
            $revision->setPrevious($previous);
            $this->objectManager->persist($previous);
            $revision->setTenantCode($previous->getTenantCode());
        }

        $this->objectManager->persist($revision);
        $this->objectManager->flush();

        return $revision;
    }

    /**
     * {@inheritdoc}
     */
    public function getRevisionContext(): RevisionContextInterface
    {
        return $this->revisionContext;
    }
}
