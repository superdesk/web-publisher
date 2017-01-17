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

namespace SWP\Component\Revision;

use SWP\Component\Revision\Model\RevisionInterface;

interface RevisionAwareInterface
{
    /**
     * @param RevisionInterface $revision
     */
    public function setRevision(RevisionInterface $revision);

    /**
     * @return RevisionInterface
     */
    public function getRevision(): RevisionInterface;

    /**
     * Create new (with modifications) object for working revision.
     *
     * @return mixed
     */
    public function fork();

    /**
     * @return string
     */
    public function getUuid(): string;

    /**
     * @param string $uuid
     *
     * @return mixed
     */
    public function setUuid(string $uuid);
}
