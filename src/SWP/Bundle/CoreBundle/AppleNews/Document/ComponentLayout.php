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

class ComponentLayout
{
    /** @var int|null */
    private $columnStart;

    /** @var int|null */
    private $columnSpan;

    /** @var Margin|null */
    private $margin;

    public function getColumnStart(): ?int
    {
        return $this->columnStart;
    }

    public function setColumnStart(?int $columnStart): void
    {
        $this->columnStart = $columnStart;
    }

    public function getColumnSpan(): ?int
    {
        return $this->columnSpan;
    }

    public function setColumnSpan(?int $columnSpan): void
    {
        $this->columnSpan = $columnSpan;
    }

    public function getMargin(): ?Margin
    {
        return $this->margin;
    }

    public function setMargin(?Margin $margin): void
    {
        $this->margin = $margin;
    }
}
