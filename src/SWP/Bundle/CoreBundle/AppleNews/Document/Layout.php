<?php

declare(strict_types=1);

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
