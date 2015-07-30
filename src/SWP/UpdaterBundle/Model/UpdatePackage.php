<?php

namespace SWP\UpdaterBundle\Model;

use Updater\Package\Package;

/**
 * Update package model class.
 */
class UpdatePackage extends Package
{
    /**
     * Construct method for class. Converts data (string via json_decode or
     * array/object) to properties.
     *
     * @param mixed $data
     */
    public function __construct($data = null)
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
