<?php

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

namespace SWP\Component\Revision;

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
