<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Twig Cache Extension.
 *
 * Twig 3 port of the abandoned asm89 twig/cache-extension (MIT).
 *
 * Copyright 2026 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 */

namespace SWP\Component\TwigCacheExtension\Exception;

class InvalidCacheLifetimeException extends \RuntimeException
{
    public function __construct($value)
    {
        parent::__construct(sprintf('Value is not a valid cache lifetime: "%s".', var_export($value, true)));
    }
}
