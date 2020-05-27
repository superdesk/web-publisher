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

class Figure extends UrlAwareComponent
{
    public const ROLE = 'figure';

    /** @var string */
    private $role = self::ROLE;

    /** @var string */
    private $caption;

    public function __construct(string $url, string $caption)
    {
        $this->caption = $caption;

        parent::__construct($url);
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }
}
