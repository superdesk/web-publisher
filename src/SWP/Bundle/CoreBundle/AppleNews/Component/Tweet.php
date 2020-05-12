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

namespace SWP\Bundle\CoreBundle\AppleNews\Component;

/**
 * The component for adding a Tweet that was posted to Twitter.
 */
class Tweet extends UrlAwareComponent
{
    public const ROLE = 'tweet';

    /** @var string */
    private $role = self::ROLE;

    public function getRole(): string
    {
        return $this->role;
    }
}
