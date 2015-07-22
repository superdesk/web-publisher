<?php

/**
 * @copyright 2015 Sourcefabric z.ú.
 * @author Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace SWP\SuperdeskBridgeBundle\Data;

use stdClass;

// TODO: Replace by entities (most likely)
/**
 * Abstract for simple Item and Package objects.
 */
abstract class AbstractDataObject extends stdClass
{
    /**
     * Construct method for class. Converts data (string via json_decode or
     * array/object) to properties.
     *
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        if (is_string($data)) {
            $objectData = json_decode($data);
        } elseif (is_array($data) || is_object($data)) {
            $objectData = $data;
        }

        foreach ($objectData as $key => $value) {
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
