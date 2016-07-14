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
namespace SWP\Component\Bridge;

use SWP\Component\Bridge\Exception\RuntimeException;
use SWP\Component\Bridge\Validator\ValidatorInterface;

interface ChainValidatorInterface
{
    /**
     * Return an array of registered validators.
     *
     * @return ValidatorInterface[] An array of validators
     */
    public function getValidators();

    /**
     * Returns a single validator by alias.
     *
     * @param string $alias
     *
     * @throws RuntimeException
     *
     * @return ValidatorInterface|null
     */
    public function getValidator($alias);

    /**
     * Adds a new validator.
     *
     * @param ValidatorInterface $validator
     * @param string             $alias
     *
     * @throws RuntimeException
     */
    public function addValidator(ValidatorInterface $validator, $alias);
}
