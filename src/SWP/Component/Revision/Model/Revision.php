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
 * Class Revision.
 */
class Revision implements RevisionInterface
{
    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     */
    protected $publishedAt;

    /**
     * @var bool
     */
    protected $isActive;

    /**
     * @var RevisionInterface
     */
    protected $previous;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var
     */
    protected $uniqueKey;

    /**
     * Revision constructor.
     */
    public function __construct()
    {
        $this->setUniqueKey(md5(serialize($this->updatedAt).random_bytes(10)));
        $this->setIsActive(true);
        $this->setStatus(self::STATE_NEW);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus(string $status)
    {
        if ($status === RevisionInterface::STATE_PUBLISHED) {
            $this->status = $status;
        } else {
            $this->status = self::STATE_NEW;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUniqueKey()
    {
        return $this->uniqueKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setUniqueKey($uniqueKey)
    {
        $this->uniqueKey = $uniqueKey;
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
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedAt(): \DateTime
    {
        return $this->publishedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishedAt(\DateTime $publishedAt)
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive(bool $isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrevious(RevisionInterface $revision)
    {
        $this->previous = $revision;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrevious()
    {
        return $this->previous;
    }
}
