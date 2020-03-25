<?php

namespace SWP\Bundle\CoreBundle\AppleNews\Component;

class Photo implements ComponentInterface
{
    /** @var string */
    private $url;

    /** @var string */
    private $role = 'photo';

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
