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

    /**
     * RevisionManager constructor.
     *
     * @param FactoryInterface         $revisionFactory
     * @param RevisionContextInterface $revisionContext
     */
    public function __construct(FactoryInterface $revisionFactory, RevisionContextInterface $revisionContext)
    {
        $this->revisionFactory = $revisionFactory;
        $this->revisionContext = $revisionContext;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(RevisionInterface $revision): RevisionInterface
    {
        // TODO: Implement publish() method.
        //
        // When new revision is published then all not modified containers/widgets need to be moved to new revision
        // All modified containers/widgets should be also moved to published revision

//        /** @var RevisionLogInterface $revisionLog */
//        $revisionLog = $this->revisionLogFactory->create();
//        $revisionLog->setEvent(RevisionLogInterface::EVENT_UPDATE);
//        $revisionLog->setObjectType($object::class);
//        $revisionLog->setObjectId($object->getId());
//        $revisionLog->setSourceRevision();
//        $revisionLog->setTargetRevision();
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
