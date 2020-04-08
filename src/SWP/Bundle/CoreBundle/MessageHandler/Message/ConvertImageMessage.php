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

namespace SWP\Bundle\CoreBundle\MessageHandler\Message;

class ConvertImageMessage implements MessageInterface
{
    /** @var int */
    private $imageId;

    /** @var int */
    private $tenantId;

    public function __construct(int $imageId, int $tenantId)
    {
        $this->imageId = $imageId;
        $this->tenantId = $tenantId;
    }

    public function getImageId(): int
    {
        return $this->imageId;
    }

    public function getTenantId(): int
    {
        return $this->tenantId;
    }

    public function toArray(): array
    {
        return [
            'tenantId' => $this->tenantId,
            'imageId' => $this->imageId,
        ];
    }
}
