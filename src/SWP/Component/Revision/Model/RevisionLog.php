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

/**
 * Class RevisionLog.
 */
class RevisionLog implements RevisionLogInterface
{
    /**
     * @var RevisionInterface
     */
    protected $targetRevision;

    /**
     * @var RevisionInterface
     */
    protected $sourceRevision;

    /**
     * @var string
     */
    protected $objectType;

    /**
     * @var int
     */
    protected $objectId;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var string
     */
    protected $event;

    /**
     * {@inheritdoc}
     */
    public function getTargetRevision(): RevisionInterface
    {
        return $this->targetRevision;
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetRevision(RevisionInterface $targetRevision)
    {
        $this->targetRevision = $targetRevision;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceRevision(): RevisionInterface
    {
        return $this->sourceRevision;
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceRevision(RevisionInterface $sourceRevision)
    {
        $this->sourceRevision = $sourceRevision;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectType(): string
    {
        return $this->objectType;
    }

    /**
     * {@inheritdoc}
     */
    public function setObjectType(string $objectType)
    {
        $this->objectType = $objectType;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectId(): int
    {
        return $this->objectId;
    }

    /**
     * {@inheritdoc}
     */
    public function setObjectId(int $objectId)
    {
        $this->objectId = $objectId;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * {@inheritdoc}
     */
    public function setEvent(string $event)
    {
        $this->event = $event;
    }
}
