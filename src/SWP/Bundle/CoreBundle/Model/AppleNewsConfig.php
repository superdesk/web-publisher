<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

class AppleNewsConfig implements AppleNewsConfigInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $apiKeyId;

    /** @var string */
    protected $apiKeySecret;

    /** @var string */
    protected $channelId;

    /** @var TenantInterface */
    protected $tenant;

    public function getId()
    {
        return $this->id;
    }

    public function getApiKeyId(): ?string
    {
        return $this->apiKeyId;
    }

    public function setApiKeyId(?string $apiKeyId): void
    {
        $this->apiKeyId = $apiKeyId;
    }

    public function getApiKeySecret(): ?string
    {
        return $this->apiKeySecret;
    }

    public function setApiKeySecret(?string $apiKeySecret): void
    {
        $this->apiKeySecret = $apiKeySecret;
    }

    public function getChannelId(): ?string
    {
        return $this->channelId;
    }

    public function setChannelId(?string $channelId): void
    {
        $this->channelId = $channelId;
    }

    public function getTenant(): TenantInterface
    {
        return $this->tenant;
    }

    public function setTenant(TenantInterface $tenant): void
    {
        $this->tenant = $tenant;
    }
}
