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
use SWP\Component\Revision\Model\RevisionLogInterface;
use SWP\Component\Revision\RevisionAwareInterface;
use SWP\Component\Revision\RevisionContextInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Model\PersistableInterface;

/**
 * Class RevisionLogSubscriber.
 */
class RevisionLogSubscriber implements EventSubscriber
{
    /**
     * @var FactoryInterface
     */
    protected $revisionLogFactory;

    /**
     * @var RevisionContextInterface
     */
    protected $revisionContext;

    /**
     * RevisionLogSubscriber constructor.
     *
     * @param FactoryInterface         $revisionLogFactory
     * @param RevisionContextInterface $revisionContext
     */
    public function __construct(FactoryInterface $revisionLogFactory, RevisionContextInterface $revisionContext)
    {
        $this->revisionLogFactory = $revisionLogFactory;
        $this->revisionContext = $revisionContext;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'postUpdate',
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if (!$object instanceof RevisionAwareInterface || !$object instanceof PersistableInterface) {
            return;
        }

        if ($object->getRevision() === $this->revisionContext->getWorkingRevision()) {
            return;
        }

        $om = $args->getObjectManager();
        /** @var RevisionLogInterface $revisionLog */
        $revisionLog = $this->revisionLogFactory->create();
        $revisionLog->setEvent(RevisionLogInterface::EVENT_UPDATE);
        $revisionLog->setObjectType(get_class($object));
        $revisionLog->setObjectId($object->getId());
        $revisionLog->setSourceRevision($object->getRevision());
        $revisionLog->setTargetRevision($this->revisionContext->getWorkingRevision());

        $object->setRevision($this->revisionContext->getWorkingRevision());

        $om->persist($revisionLog);
        $om->flush();
    }
}
