<?php

namespace SWP\Bundle\CoreBundle\AppleNews\Component;

class Body implements ComponentInterface
{
    public const ROLE = 'body';

    public const FORMAT = 'html';

    /** @var string */
    private $text;

    /** @var string */
    private $role = self::ROLE;

    /** @var string */
    private $format;

    public function __construct(string $text, string $format = self::FORMAT)
    {
        $this->text = $text;
        $this->format = $format;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
