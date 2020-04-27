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

class Layout
{
    /** @var int */
    private $columns;

    /** @var int */
    private $width;

    /** @var int */
    private $gutter;

    /** @var int */
    private $margin;

    public function __construct(int $columns, int $width, int $gutter, int $margin)
    {
        $this->columns = $columns;
        $this->width = $width;
        $this->gutter = $gutter;
        $this->margin = $margin;
    }

    public function getColumns(): int
    {
        return $this->columns;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getGutter(): int
    {
        return $this->gutter;
    }

    public function getMargin(): int
    {
        return $this->margin;
    }
}
