<?php

namespace SWP\Component\Bridge\Validator;

use Symfony\Component\HttpFoundation\Request;

/**
 * Validates Request data against specific schema.
 */
interface ValidatorInterface
{
    /**
     * Validates data against specific schema.
     *
     * @param Request $request The request to validate
     *
     * @return bool If the returned value is 'true', validation
     *              succeeded, otherwise it failed.
     */
    public function isValid(Request $request);

    /**
     * Gets current validator schema.
     *
     * @return string
     */
    public function getSchema();

    /**
     * Sets schema against which the data will be validated.
     * 
     * @param string $schema
     */
    public function setSchema($schema = '');

    /**
     * Gets validator format.
     *
     * @return string
     */
    public function getFormat();
}
