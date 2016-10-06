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

namespace SWP\Component\Bridge\Validator;

use JsonSchema\Validator;
use Psr\Log\LoggerInterface;

abstract class JsonValidator implements ValidatorInterface, ValidatorOptionsInterface
{
    /**
     * @var string
     */
    protected $schema = '';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * JsonValidator constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data)
    {
        $validator = new Validator();
        $validator->check(json_decode($data), json_decode($this->getSchema()));

        if ($validator->isValid()) {
            return true;
        }

        $this->logger->error(implode(', ', array_map(function ($error) {
            return sprintf('"%s" %s', $error['property'], $error['message']);
        }, $validator->getErrors())));

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema()
    {
        return $this->schema;
    }
}
