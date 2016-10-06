<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Bridge;

use SWP\Component\Bridge\Exception\RuntimeException;
use SWP\Component\Bridge\Validator\ValidatorInterface;

class ValidatorChain implements ValidatorInterface, ChainValidatorInterface
{
    /**
     * @var ValidatorInterface[]
     */
    private $validators = [];

    /**
     * {@inheritdoc}
     */
    public function addValidator(ValidatorInterface $validator, $alias)
    {
        if (isset($this->validators[$alias])) {
            throw new RuntimeException(sprintf('"%s" validator is already registered!', $alias));
        }

        $this->validators[$alias] = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidator($alias)
    {
        if (!isset($this->validators[$alias])) {
            throw new RuntimeException(sprintf(
                'Unknown validator selected ("%s"), available are: %s',
                $alias,
                implode(', ', array_keys($this->validators))
            ));
        }

        return $this->validators[$alias];
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($value)
    {
        foreach ($this->validators as $validator) {
            if ($validator->isValid($value)) {
                return true;
            }
        }

        return false;
    }
}
