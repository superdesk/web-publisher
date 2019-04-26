<?php

namespace SWP\Component\Seo\Model;

use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface SeoImageInterface extends PersistableInterface, TimestampableInterface
{
    public function getKey(): string;
}
