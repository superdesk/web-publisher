<?php

namespace SWP\Bundle\CoreBundle\AppleNews\Document;

class ComponentTextStyles
{
    /** @var ComponentTextStyle */
    private $default;

    public function getDefault(): ComponentTextStyle
    {
        return $this->default;
    }

    public function setDefault(ComponentTextStyle $default): void
    {
        $this->default = $default;
    }
}
