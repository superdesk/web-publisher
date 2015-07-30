<?php

namespace SWP\UpdaterBundle\Model;

use Updater\Package\Package;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Update package model class.
 *
 * @Hateoas\Relation("self", href = "expr('/api/updates/latest')")
 */
class UpdatePackage extends Package
{
    /**
     * Construct method for class.
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Get property from object.
     *
     * @param string $property Property name
     *
     * @return mixed Property value
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
}
