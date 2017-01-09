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

trait RevisionAwareTrait
{
    /**
     * @var RevisionInterface
     */
    protected $revision;

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @param RevisionInterface $revision
     */
    public function setRevision(RevisionInterface $revision)
    {
        $this->revision = $revision;
    }

    /**
     * @return RevisionInterface
     */
    public function getRevision(): RevisionInterface
    {
        return $this->revision;
    }

    /**
     * @param null $object
     *
     * @return RevisionAwareTrait
     */
    public function fork($object = null)
    {
        return clone $this;
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }
}
