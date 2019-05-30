<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Webhook Component.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Webhook\Model;

use SWP\Component\Common\Model\EnableableInterface;

interface WebhookInterface extends EnableableInterface
{
    public function getUrl(): ?string;

    public function setUrl(string $url): void;
}
