<?php

/**
 * This file is part of the Superdesk Web Publisher Storage Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\StorageBundle\DependencyInjection\Factory;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\StorageBundle\DependencyInjection\Driver\ORMDriver;
use SWP\Bundle\StorageBundle\DependencyInjection\Driver\PHPCRDriver;
use SWP\Bundle\StorageBundle\DependencyInjection\Factory\DriverFactory;
use SWP\Bundle\StorageBundle\Drivers;
use SWP\Component\Storage\DependencyInjection\Factory\DriverFactoryInterface;
use SWP\Component\Storage\Exception\InvalidDriverException;

/**
 * @mixin DriverFactory
 */
class DriverFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(DriverFactory::class);
    }

    public function it_implements_driver_factory_interface()
    {
        $this->shouldImplement(DriverFactoryInterface::class);
    }

    public function it_should_create_orm_driver()
    {
        $this->createDriver(Drivers::DRIVER_DOCTRINE_ORM)->shouldHaveType(ORMDriver::class);
    }

    public function it_should_create_phpcr_driver()
    {
        $this->createDriver(Drivers::DRIVER_DOCTRINE_PHPCR_ODM)->shouldHaveType(PHPCRDriver::class);
    }

    public function it_should_throw_an_exception_when_invalid_driver()
    {
        $this
            ->shouldThrow(InvalidDriverException::class)
            ->duringCreateDriver('fake');
    }
}
