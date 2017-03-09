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

namespace SWP\Bundle\CoreBundle\EventSubscriber;

use SWP\Bundle\CoreBundle\Repository\RevisionAwareContainerRepositoryInterface;
use SWP\Bundle\RevisionBundle\Event\RevisionPublishedEvent;
use SWP\Bundle\RevisionBundle\Events;
use SWP\Component\Revision\Model\RevisionInterface;
use SWP\Bundle\RevisionBundle\Model\RevisionLogInterface;
use SWP\Component\Revision\RevisionAwareInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Model\PersistableInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RevisionsSubscriber implements EventSubscriberInterface
{
    /**
     * @var RevisionAwareContainerRepositoryInterface
     */
    protected $repository;

    /**
     * @var FactoryInterface
     */
    protected $revisionLogFactory;

    /**
     * RevisionsSubscriber constructor.
     *
     * @param RevisionAwareContainerRepositoryInterface $repository
     * @param FactoryInterface                          $revisionLogFactory
     */
    public function __construct(
        RevisionAwareContainerRepositoryInterface $repository,
        FactoryInterface $revisionLogFactory
    ) {
        $this->repository = $repository;
        $this->revisionLogFactory = $revisionLogFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::REVISION_PUBLISH => 'publish',
        ];
    }

    /**
     * @param RevisionPublishedEvent $event
     */
    public function publish(RevisionPublishedEvent $event)
    {
        /** @var RevisionInterface $revision */
        $revision = $event->getRevision();
        if (null === $revision->getPrevious()) {
            return;
        }

        // new revision containers id's
        $newRevisionContainers = $this->repository->getIds($revision)->getQuery()->getResult();
        $ids = [];
        foreach ($newRevisionContainers as $container) {
            $ids[] = $container['uuid'];
        }

        $containers = $this->repository->getContainerWithoutProvidedIds($ids, $revision)->getQuery()->getResult();
        /** @var ContainerInterface|RevisionAwareInterface $container */
        foreach ($containers as $container) {
            $container->setRevision($revision);
            $this->log($container, $revision);
        }

        $this->repository->flush();
    }

    /**
     * @param $object
     * @param RevisionInterface $revision
     */
    private function log($object, $revision)
    {
        if (!$object instanceof PersistableInterface || !$object instanceof RevisionAwareInterface) {
            return;
        }

        /** @var RevisionLogInterface $revisionLog */
        $revisionLog = $this->revisionLogFactory->create();
        $revisionLog->setEvent(RevisionLogInterface::EVENT_UPDATE);
        $revisionLog->setObjectType(get_class($object));
        $revisionLog->setObjectId($object->getId());
        if (null !== $previousRevision = $revision->getPrevious()) {
            $revisionLog->setSourceRevision($previousRevision);
        }
        $revisionLog->setTargetRevision($revision);

        $this->repository->persist($revisionLog);
    }
}
