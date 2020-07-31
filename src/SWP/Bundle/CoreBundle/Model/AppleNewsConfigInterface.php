<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Component\Storage\Model\PersistableInterface;

interface AppleNewsConfigInterface extends PersistableInterface
{
    public function getApiKeyId(): ?string;

    public function setApiKeyId(?string $apiKeyId): void;

    public function getApiKeySecret(): ?string;

    public function setApiKeySecret(?string $apiKeySecret): void;

    public function getChannelId(): ?string;

    public function setChannelId(?string $channelId): void;

    public function getTenant(): TenantInterface;

    public function setTenant(TenantInterface $tenant): void;
}
