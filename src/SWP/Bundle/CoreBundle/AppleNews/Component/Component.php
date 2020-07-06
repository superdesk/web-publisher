<?php

namespace SWP\Bundle\CoreBundle\AppleNews\Component;

abstract class Component implements ComponentInterface
{
    /** @var string */
    protected $text;

    /** @var string|null */
    protected $layout;

    /** @var array */
    protected $inlineTextStyles;

    public function __construct(string $text, string $layout = null, array $inlineTextStyles = [])
    {
        $this->text = $text;
        $this->layout = $layout;
        $this->inlineTextStyles = $inlineTextStyles;
    }

    public function getText(): string
    {
        return $this->text;
    }

    abstract public function getRole(): string;

    public function getLayout(): ?string
    {
        return $this->layout;
    }

    public function getInlineTextStyles(): array
    {
        return $this->inlineTextStyles;
    }
}
