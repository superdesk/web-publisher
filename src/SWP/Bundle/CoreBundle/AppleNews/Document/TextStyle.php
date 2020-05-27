<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\AppleNews\Document;

class TextStyle
{
    private $textColor;

    public function __construct(string $textColor)
    {
        $this->textColor = $textColor;
    }

    public function getTextColor(): string
    {
        return $this->textColor;
    }
}
