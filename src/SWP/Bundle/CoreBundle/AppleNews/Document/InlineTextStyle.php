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

namespace SWP\Bundle\CoreBundle\AppleNews\Document;

class InlineTextStyle
{
    /** @var int */
    private $rangeStart = 0;

    /** @var int */
    private $rangeLength;

    /** @var TextStyle */
    private $textStyle;

    public function getRangeStart(): int
    {
        return $this->rangeStart;
    }

    public function setRangeStart(int $rangeStart): void
    {
        $this->rangeStart = $rangeStart;
    }

    public function getRangeLength(): int
    {
        return $this->rangeLength;
    }

    public function setRangeLength(int $rangeLength): void
    {
        $this->rangeLength = $rangeLength;
    }

    public function getTextStyle(): TextStyle
    {
        return $this->textStyle;
    }

    public function setTextStyle(TextStyle $textStyle): void
    {
        $this->textStyle = $textStyle;
    }
}
