<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Manager;

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
     */
    public function __construct(
        FactoryInterface $revisionFactory,
        RevisionContextInterface $revisionContext,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->revisionFactory = $revisionFactory;
        $this->revisionContext = $revisionContext;
        $this->eventDispatcher = $eventDispatcher;
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
        $this->flush();

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
            $this->persist($previous);
            $revision->setTenantCode($previous->getTenantCode());
        }

        $this->persist($revision);
        $this->flush();

        return $revision;
    }

    /**
     * {@inheritdoc}
     */
    public function getRevisionContext(): RevisionContextInterface
    {
        return $this->revisionContext;
    }

    /**
     * {@inheritdoc}
     */
    public function setObjectManager(EntityManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function merge($object)
    {
        if (null !== $this->objectManager) {
            return $this->objectManager->merge($object);
        }
    }

    private function persist($object)
    {
        if (null !== $this->objectManager) {
            $this->objectManager->persist($object);
        }
    }

    private function flush()
    {
        if (null !== $this->objectManager) {
            $this->objectManager->flush();
        }
    }
}
