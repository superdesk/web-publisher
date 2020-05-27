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

class Margin
{
    /** @var int|null */
    private $bottom;

    /** @var int|null */
    private $top;

    public function __construct(int $bottom = null, int $top = null)
    {
        $this->bottom = $bottom;
        $this->top = $top;
    }

    public function getBottom(): ?int
    {
        return $this->bottom;
    }

    public function getTop(): ?int
    {
        return $this->top;
    }
}
