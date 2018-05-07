<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Output Channel Component.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\OutputChannel\Model;

use SWP\Component\Common\Model\SoftDeletableInterface;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface ExternalArticleInterface extends PersistableInterface, SoftDeletableInterface, TimestampableInterface
{
    /**
     * @param int $id
     */
    public function setId(int $id): void;

    /**
     * @return string
     */
    public function getExternalId(): string;

    /**
     * @param string $externalId
     */
    public function setExternalId(string $externalId): void;

    /**
     * @return string
     */
    public function getLiveUrl(): ?string;

    /**
     * @param string $liveUrl
     */
    public function setLiveUrl(string $liveUrl): void;

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @param string $status
     */
    public function setStatus(string $status): void;

    /**
     * @return \DateTime
     */
    public function getPublishedAt(): ?\DateTime;

    /**
     * @param \DateTime $publishedAt
     */
    public function setPublishedAt(\DateTime $publishedAt): void;

    /**
     * @return \DateTime
     */
    public function getUnpublishedAt(): ?\DateTime;

    /**
     * @param \DateTime $unpublishedAt
     */
    public function setUnpublishedAt(\DateTime $unpublishedAt): void;
}
