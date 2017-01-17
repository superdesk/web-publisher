<?php

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

namespace SWP\Bundle\RevisionBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use SWP\Component\Revision\Manager\RevisionManagerInterface;
use SWP\Component\Revision\RevisionAwareInterface;
use SWP\Component\Revision\RevisionContextInterface;

/**
 * Class RevisionLogSubscriber.
 */
class RevisionSubscriber implements EventSubscriber
{
    /**
     * @var RevisionManagerInterface
     */
    protected $revisionManager;

    /**
     * @var RevisionContextInterface
     */
    protected $revisionContext;

    /**
     * RevisionSubscriber constructor.
     *
     * @param RevisionManagerInterface $revisionManager
     */
    public function __construct(RevisionManagerInterface $revisionManager)
    {
        $this->revisionManager = $revisionManager;
        $this->revisionContext = $revisionManager->getRevisionContext();
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->createWorkingRevision($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createWorkingRevision($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    private function createWorkingRevision(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if (!$object instanceof RevisionAwareInterface) {
            return;
        }

        $workingRevision = $this->revisionContext->getWorkingRevision();
        if (null === $workingRevision) {
            $workingRevision = $this->revisionManager->create($this->revisionContext->getPublishedRevision());
            $om = $args->getObjectManager();
            $this->revisionContext->setWorkingRevision($workingRevision);
            $om->persist($workingRevision);
            $om->flush();
        }
    }
}
