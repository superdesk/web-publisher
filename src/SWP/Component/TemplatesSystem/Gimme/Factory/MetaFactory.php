<?php

/**
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\TemplatesSystem\Gimme\Factory;

use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

/**
 * Class MetaFactory.
 */
class MetaFactory implements MetaFactoryInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * MetaFactory constructor.
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function create($value, array $configuration = null)
    {
        if (null === $configuration) {
            $configuration = $this->context->getConfigurationForValue($value);
        }

        return new Meta($this->context, $value, $configuration);
    }
}
