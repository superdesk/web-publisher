<?php

/**
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

namespace SWP\Component\Bridge\Validator;

use JsonSchema\Validator;

abstract class JsonValidator implements ValidatorInterface, ValidatorOptionsInterface
{
    /**
     * @var string
     */
    protected $schema = '';

    /**
     * {@inheritdoc}
     */
    public function isValid($data)
    {
        $validator = new Validator();
        $validator->check(json_decode($data), json_decode($this->getSchema()));

        return $validator->isValid();
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema()
    {
        return $this->schema;
    }
}
