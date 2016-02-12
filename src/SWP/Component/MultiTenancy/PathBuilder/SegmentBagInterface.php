<?php

namespace SWP\Component\MultiTenancy\PathBuilder;

use SWP\Component\MultiTenancy\Exception\SegmentNotFoundException;

interface SegmentBagInterface
{
    /**
     * @param $name
     *
     * @return string
     *
     * @throws SegmentNotFoundException
     */
    public function get($name);
}
