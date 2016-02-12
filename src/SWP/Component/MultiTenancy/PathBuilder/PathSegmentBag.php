<?php

namespace SWP\Component\MultiTenancy\PathBuilder;

use SWP\Component\MultiTenancy\Exception\SegmentNotFoundException;

class PathSegmentBag implements SegmentBagInterface
{
    /**
     * @var array
     */
    protected $pathSegments;

    /**
     * PathSegmentBag constructor.
     *
     * @param array $pathSegments
     */
    public function __construct(array $pathSegments = [])
    {
        $this->pathSegments = $pathSegments;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        $name = strtolower($name);
        if (!isset($name, $this->pathSegments)) {
            if (!$name) {
                throw new SegmentNotFoundException($name);
            }

            $alternatives = array();
            foreach ($this->pathSegments as $key => $segmentValue) {
                $lev = levenshtein($name, $key);
                if ($lev <= strlen($name) / 3 || false !== strpos($key, $name)) {
                    $alternatives[] = $key;
                }
            }

            throw new SegmentNotFoundException($name, null, $alternatives);
        }

        return $this->pathSegments[$name];
    }
}
