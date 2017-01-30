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

interface RevisionContextInterface
{
    /**
     * @param RevisionInterface $revision
     *
     * @return mixed
     */
    public function setCurrentRevision(RevisionInterface $revision);

    /**
     * @return RevisionInterface
     */
    public function getCurrentRevision(): RevisionInterface;

    /**
     * @param RevisionInterface $revision
     */
    public function setPublishedRevision(RevisionInterface $revision);

    /**
     * @return RevisionInterface
     */
    public function getPublishedRevision(): RevisionInterface;

    /**
     * @param RevisionInterface $revision
     */
    public function setWorkingRevision(RevisionInterface $revision);

    /**
     * @return null|RevisionInterface
     */
    public function getWorkingRevision();
}
