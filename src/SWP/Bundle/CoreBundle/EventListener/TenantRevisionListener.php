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

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\RevisionBundle\RevisionContext;
use SWP\Component\Revision\Model\RevisionInterface;
use SWP\Component\Revision\Repository\RevisionRepositoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class TenantRevisionListener.
 */
class TenantRevisionListener
{
    /**
     * @var RepositoryInterface
     */
    protected $revisionRepository;

    /**
     * @var RevisionContext
     */
    protected $revisionContext;

    /**
     * TenantRevisionListener constructor.
     *
     * @param RevisionRepositoryInterface $revisionRepository
     * @param RevisionContext             $revisionContext
     */
    public function __construct(RevisionRepositoryInterface $revisionRepository, RevisionContext $revisionContext)
    {
        $this->revisionRepository = $revisionRepository;
        $this->revisionContext = $revisionContext;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $currentRevision = null;
        $revisionKey = $request->cookies->get(
            RevisionContext::REVISION_PARAMETER_NAME,
            $request->request->get(
                RevisionContext::REVISION_PARAMETER_NAME,
                $request->query->get(RevisionContext::REVISION_PARAMETER_NAME, false)
            )
        );

        /* @var RevisionInterface $workingRevision */
        $publishedRevision = $this->revisionRepository->getPublishedRevision()->getQuery()->getOneOrNullResult();
        $this->revisionContext->setPublishedRevision($publishedRevision);
        /** @var RevisionInterface $workingRevision */
        $workingRevision = $this->revisionRepository->getWorkingRevision()->getQuery()->getOneOrNullResult();
        if (null !== $workingRevision) {
            $this->revisionContext->setWorkingRevision($workingRevision);
        }

        if (null !== $revisionKey) {
            if (null !== $workingRevision && $workingRevision->getUniqueKey() === $revisionKey) {
                $currentRevision = $workingRevision;
            } else {
                $currentRevision = $this->revisionRepository->getByKey($revisionKey)->getQuery()->getOneOrNullResult();
            }
        }

        if (null === $currentRevision) {
            $currentRevision = $publishedRevision;
        }

        $this->revisionContext->setCurrentRevision($currentRevision);
    }
}
