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

class Heading extends Component
{
    public const ROLE = 'heading';

    public const FORMAT = 'html';

    /** @var string */
    private $format;

    /** @var string */
    private $role = self::ROLE;

    public function __construct(string $text, int $headingType = 1, string $format = self::FORMAT)
    {
        $this->role .= $headingType;
        $this->format = $format;

        parent::__construct($text, null);
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
