<?php

namespace SWP\Bundle\CoreBundle\AppleNews\Component;

abstract class Component implements ComponentInterface
{
    /** @var string */
    protected $text;

    /** @var string|null */
    protected $layout;

    public function __construct(string $text, string $layout = null)
    {
        $this->text = $text;
        $this->layout = $layout;
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
}
