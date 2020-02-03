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

namespace SWP\Bundle\CoreBundle\Webhook\Message;

class WebhookMessage
{
    /** @var string */
    private $url;

    /** @var string */
    private $body;

    /** @var array */
    private $metadata;

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }
}
