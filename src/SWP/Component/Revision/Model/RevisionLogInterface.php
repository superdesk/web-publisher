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

namespace SWP\Component\Revision\Model;

interface RevisionLogInterface
{
    const EVENT_UPDATE = 'update';

    const EVENT_PUBLISH = 'publish';

    /**
     * @return RevisionInterface
     */
    public function getTargetRevision(): RevisionInterface;

    /**
     * @param RevisionInterface $targetRevision
     */
    public function setTargetRevision(RevisionInterface $targetRevision);

    /**
     * @return RevisionInterface
     */
    public function getSourceRevision(): RevisionInterface;

    /**
     * @param RevisionInterface $sourceRevision
     */
    public function setSourceRevision(RevisionInterface $sourceRevision);

    /**
     * @return string
     */
    public function getObjectType(): string;

    /**
     * @param string $objectType
     */
    public function setObjectType(string $objectType);

    /**
     * @return int
     */
    public function getObjectId(): int;

    /**
     * @param int $objectId
     */
    public function setObjectId(int $objectId);

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime;

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return string
     */
    public function getEvent(): string;

    /**
     * @param string $event
     */
    public function setEvent(string $event);
}
