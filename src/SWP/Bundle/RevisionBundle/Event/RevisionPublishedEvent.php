<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Publisher Revision Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\RevisionBundle\Event;

use SWP\Component\Revision\Model\RevisionInterface;
use Symfony\Component\EventDispatcher\Event;

class RevisionPublishedEvent extends Event
{
    /**
     * @var RevisionInterface
     */
    protected $revision;

    /**
     * RevisionPublishedEvent constructor.
     *
     * @param RevisionInterface $revision
     */
    public function __construct(RevisionInterface $revision)
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
}
