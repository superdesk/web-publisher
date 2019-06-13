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

use SWP\Component\Common\Model\SoftDeletableTrait;
use SWP\Component\Common\Model\TimestampableTrait;

class ExternalArticle implements ExternalArticleInterface
{
    use SoftDeletableTrait;
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $externalId;

    /**
     * @var string
     */
    protected $liveUrl;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var \DateTime
     */
    protected $publishedAt;

    /**
     * @var \DateTime
     */
    protected $unpublishedAt;

    /**
     * @var array|null
     */
    protected $extra;

    /**
     * ExternalArticle constructor.
     */
    public function __construct()
    {
        $this->setExtra([]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getExternalId(): string
    {
        return $this->externalId;
    }

    /**
     * {@inheritdoc}
     */
    public function setExternalId(string $externalId): void
    {
        $this->externalId = $externalId;
    }

    /**
     * {@inheritdoc}
     */
    public function getLiveUrl(): ?string
    {
        return $this->liveUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setLiveUrl(string $liveUrl): void
    {
        $this->liveUrl = $liveUrl;
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
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishedAt(\DateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnpublishedAt(): ?\DateTime
    {
        return $this->unpublishedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnpublishedAt(\DateTime $unpublishedAt): void
    {
        $this->unpublishedAt = $unpublishedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtra(): ?array
    {
        return $this->extra;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtra(?array $extra): void
    {
        $this->extra = $extra;
    }
}
