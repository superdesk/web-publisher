<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\MultiTenancy\Factory;

use SWP\Component\Common\Generator\GeneratorInterface;
use SWP\Component\MultiTenancy\Model\OrganizationInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class OrganizationFactory implements OrganizationFactoryInterface
{
    /**
     * @var GeneratorInterface
     */
    protected $generator;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * OrganizationFactory constructor.
     *
     * @param FactoryInterface   $factory
     * @param GeneratorInterface $generator
     */
    public function __construct(FactoryInterface $factory, GeneratorInterface $generator)
    {
        $this->factory = $factory;
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function createWithCode()
    {
        /** @var OrganizationInterface $organization */
        $organization = $this->create();
        $organization->setCode($this->generator->generate(6));

        return $organization;
    }
}
