<?php

declare(strict_types=1);

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

use SWP\Component\Revision\Context\RevisionContext;
use SWP\Component\Revision\Context\RevisionContextInterface;
use SWP\Component\Revision\Model\RevisionInterface;
use SWP\Component\Revision\Repository\RevisionRepositoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @param RevisionContextInterface    $revisionContext
     */
    public function __construct(RevisionRepositoryInterface $revisionRepository, RevisionContextInterface $revisionContext)
    {
        $this->revisionRepository = $revisionRepository;
        $this->revisionContext = $revisionContext;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->setRevisions($this->getRevisionKeyFromRequest($event->getRequest()));
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function getRevisionKeyFromRequest(Request $request)
    {
        return $request->cookies->get(
            RevisionContext::REVISION_PARAMETER_NAME,
            $request->request->get(
                RevisionContext::REVISION_PARAMETER_NAME,
                $request->query->get(RevisionContext::REVISION_PARAMETER_NAME, null)
            )
        );
    }

    /**
     * @param string|null $revisionKey
     */
    public function setRevisions(string $revisionKey = null)
    {
        $currentRevision = null;
        /* @var RevisionInterface $workingRevision */
        $publishedRevision = $this->revisionRepository->getPublishedRevision()->getQuery()->getOneOrNullResult();
        $this->revisionContext->setPublishedRevision($publishedRevision);
        /** @var RevisionInterface $workingRevision */
        $workingRevision = $this->revisionRepository->getWorkingRevision()->getQuery()->getOneOrNullResult();
        $this->revisionContext->setWorkingRevision($workingRevision);

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
