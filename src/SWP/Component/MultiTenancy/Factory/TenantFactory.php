<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\MultiTenancy\Factory;

use SWP\Component\Common\Generator\GeneratorInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;

/**
 * Class TenantFactory.
 */
class TenantFactory implements TenantFactoryInterface
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var GeneratorInterface
     */
    protected $generator;

    /**
     * TenantFactory constructor.
     *
     * @param string             $className
     * @param GeneratorInterface $generator
     */
    public function __construct($className, GeneratorInterface $generator)
    {
        $this->className = $className;
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        /** @var TenantInterface $tenant */
        $tenant = new $this->className();
        $tenant->setCode($this->generator->generate(6));

        return $tenant;
    }
}
