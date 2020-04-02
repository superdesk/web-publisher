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

class Heading implements ComponentInterface
{
    public const ROLE = 'heading';

    public const FORMAT = 'html';

    /** @var string */
    private $text;

    /** @var string */
    private $role = self::ROLE;

    /** @var string */
    private $format;

    public function __construct(string $text, int $headingType = 1, string $format = self::FORMAT)
    {
        $this->text = $text;
        $this->format = $format;
        $this->role .= $headingType;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
