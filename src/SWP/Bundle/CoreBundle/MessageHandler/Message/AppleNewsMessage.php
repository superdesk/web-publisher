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

abstract class AppleNewsMessage implements MessageInterface
{
    /** @var int */
    private $articleId;

    /** @var int */
    private $tenantId;

    public function __construct(int $articleId, int $tenantId)
    {
        $this->articleId = $articleId;
        $this->tenantId = $tenantId;
    }

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function getTenantId(): int
    {
        return $this->tenantId;
    }

    public function toArray(): array
    {
        return [
            'articleId' => $this->articleId,
            'tenantId' => $this->tenantId,
        ];
    }
}
