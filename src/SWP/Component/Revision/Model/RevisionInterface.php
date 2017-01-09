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

interface RevisionInterface
{
    const STATE_PUBLISHED = 'published';

    const STATE_NEW = 'new';

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime;

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime;

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * @return \DateTime
     */
    public function getPublishedAt(): \DateTime;

    /**
     * @param \DateTime $publishedAt
     */
    public function setPublishedAt(\DateTime $publishedAt);

    /**
     * @return bool
     */
    public function isIsActive(): bool;

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive);

    /**
     * @param RevisionInterface $revision
     */
    public function setPrevious(RevisionInterface $revision);

    /**
     * @return RevisionInterface
     */
    public function getPrevious(): RevisionInterface;

    /**
     * @return string
     */
    public function getUniqueKey();

    /**
     * @param string $uniqueKey
     */
    public function setUniqueKey($uniqueKey);

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @param string $status
     */
    public function setStatus(string $status);
}
