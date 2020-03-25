<?php

namespace SWP\Bundle\CoreBundle\AppleNews\Component;

class Image implements ComponentInterface
{
    public const ROLE = 'image';

    /** @var string */
    private $url;

    /** @var string */
    private $role = self::ROLE;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}
