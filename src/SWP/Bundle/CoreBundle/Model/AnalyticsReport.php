<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Model;

use DateTime;
use SWP\Component\Common\Model\TimestampableTrait;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;

class AnalyticsReport implements AnalyticsReportInterface
{
    use TimestampableTrait;
    use TenantAwareTrait;

    /** @var int */
    protected $id;

    /** @var string */
    protected $assetId;

    /** @var string */
    protected $fileExtension;

    /** @var UserInterface */
    protected $user;

    /** @var string */
    protected $status = AnalyticsReportInterface::STATUS_PROCESSING;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAssetId(): string
    {
        return $this->assetId;
    }

    public function setAssetId(string $assetId): void
    {
        $this->assetId = $assetId;
    }

    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    public function setFileExtension($fileExtension): void
    {
        $this->fileExtension = $fileExtension;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
