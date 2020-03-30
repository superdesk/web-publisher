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

class ComponentTextStyle
{
    private $backgroundColor;

    private $fontName;

    public function __construct(string $backgroundColor, string $fontName)
    {
        $this->backgroundColor = $backgroundColor;
        $this->fontName = $fontName;
    }

    public function getBackgroundColor(): string
    {
        return $this->backgroundColor;
    }

    public function getFontName(): string
    {
        return $this->fontName;
    }
}
