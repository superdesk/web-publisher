<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Revision\Manager;

use Doctrine\ORM\EntityManagerInterface;
use SWP\Component\Revision\RevisionContextInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Revision\Model\RevisionInterface;

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

    /** @var EntityManagerInterface */
    protected $objectManager;

    /**
     * RevisionManager constructor.
     *
     * @param FactoryInterface         $revisionFactory
     * @param RevisionContextInterface $revisionContext
     */
    public function __construct(
        FactoryInterface $revisionFactory,
        RevisionContextInterface $revisionContext
    ) {
        $this->revisionFactory = $revisionFactory;
        $this->revisionContext = $revisionContext;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(RevisionInterface $revision, RevisionInterface $workingRevision = null): RevisionInterface
    {
        $revision->setStatus(RevisionInterface::STATE_PUBLISHED);
        $revision->setPublishedAt(new \DateTime());
        $this->persist($revision);

        // When new revision is published then all not modified containers/widgets need to be moved to new revision
        // All modified containers/widgets should be also moved to published revision

//        /** @var RevisionLogInterface $revisionLog */
//        $revisionLog = $this->revisionLogFactory->create();
//        $revisionLog->setEvent(RevisionLogInterface::EVENT_UPDATE);
//        $revisionLog->setObjectType($object::class);
//        $revisionLog->setObjectId($object->getId());
//        $revisionLog->setSourceRevision();
//        $revisionLog->setTargetRevision();

        $this->revisionContext->setPublishedRevision($revision);
        if (null === $workingRevision) {
            $workingRevision = $this->create($revision);
            $this->revisionContext->setWorkingRevision($workingRevision);

            return $revision;
        }

        $this->revisionContext->setWorkingRevision($workingRevision);
        $this->persist($workingRevision);
        $this->flush();

        return $revision;
    }

    /**
     * {@inheritdoc}
     */
    public function rollback(RevisionInterface $revision): RevisionInterface
    {
        // TODO: Implement rollback() method.
        //
        // On rollback modified elements (containers/widgets) need to rejected and not modified moved to previous revision
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
