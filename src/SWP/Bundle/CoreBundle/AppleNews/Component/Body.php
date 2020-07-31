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

class Body extends Component
{
    public const FORMAT = 'html';

    public const ROLE = 'body';

    /** @var string */
    private $role = self::ROLE;

    /** @var string */
    private $format;

    public function __construct(string $text, string $layout = null, string $format = self::FORMAT)
    {
        $this->format = $format;

        parent::__construct($text, $layout);
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}
