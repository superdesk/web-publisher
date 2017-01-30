<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Publisher Revision Component.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Revision\Context;

use SWP\Component\Revision\Model\RevisionInterface;

/**
 * Class RevisionContext.
 */
class RevisionContext implements RevisionContextInterface
{
    const REVISION_PARAMETER_NAME = 'swp_revision_key';

    /**
     * @var RevisionInterface
     */
    protected $currentRevision;

    /**
     * @var RevisionInterface
     */
    protected $publishedRevision;

    /**
     * @var RevisionInterface
     */
    protected $workingRevision;

    /**
     * {@inheritdoc}
     */
    public function getCurrentRevision(): RevisionInterface
    {
        return $this->currentRevision;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentRevision(RevisionInterface $revision)
    {
        $this->currentRevision = $revision;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedRevision(): RevisionInterface
    {
        return $this->publishedRevision;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishedRevision(RevisionInterface $revision)
    {
        $this->publishedRevision = $revision;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkingRevision()
    {
        return $this->workingRevision;
    }

    /**
     * {@inheritdoc}
     */
    public function setWorkingRevision(RevisionInterface $revision)
    {
        $this->workingRevision = $revision;
    }
}
