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

class ContentPushMessage
{
    /** @var int */
    private $tenantId;

    /** @var string */
    private $content;

    public function __construct(int $tenantId, string $content)
    {
        $this->tenantId = $tenantId;
        $this->content = $content;
    }

    public function getTenantId(): int
    {
        return $this->tenantId;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
