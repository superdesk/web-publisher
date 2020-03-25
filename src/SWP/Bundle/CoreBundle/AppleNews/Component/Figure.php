<?php

namespace SWP\Bundle\CoreBundle\AppleNews\Component;

class Figure implements ComponentInterface
{
    public const ROLE = 'figure';

    /** @var string */
    private $url;

    /** @var string */
    private $role = self::ROLE;

    /** @var string */
    private $caption;

    public function __construct(string $url, string $caption)
    {
        $this->url = $url;
        $this->caption = $caption;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }
}
