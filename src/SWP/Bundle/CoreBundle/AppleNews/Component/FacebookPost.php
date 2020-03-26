<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\AppleNews\Component;

/**
 * The component for adding a Facebook post.
 */
class FacebookPost implements ComponentInterface
{
    public const ROLE = 'facebook_post';

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
