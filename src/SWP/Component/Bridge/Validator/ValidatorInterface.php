<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\Bridge\Validator;

/**
 * Validates value.
 */
interface ValidatorInterface
{
    /**
     * Validates value against specific schema.
     *
     * @param string $value The data string to validate
     *
     * @return bool If the returned value is 'true', validation
     *              succeeded, otherwise it failed.
     */
    public function isValid($value);
}
