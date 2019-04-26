<?php

declare(strict_types=1);

namespace SWP\Component\Seo\Model;

use SWP\Component\Common\Model\TimestampableTrait;

class SeoImage implements SeoImageInterface
{
    use TimestampableTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $key;

    public function getId(): string
    {
        return $this->id;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }
}
