<?php

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
