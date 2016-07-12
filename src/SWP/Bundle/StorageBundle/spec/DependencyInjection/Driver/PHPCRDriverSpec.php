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
namespace spec\SWP\Bundle\StorageBundle\DependencyInjection\Driver;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\StorageBundle\DependencyInjection\Driver\PHPCRDriver;
use SWP\Component\Storage\DependencyInjection\Driver\PersistenceDriverInterface;
use Symfony\Component\DependencyInjection\Parameter;

/**
 * @mixin PHPCRDriver
 */
class PHPCRDriverSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(PHPCRDriver::class);
    }

    public function it_implements_driver_interface()
    {
        $this->shouldImplement(PersistenceDriverInterface::class);
    }

    public function it_returns_default_object_manager_service_name()
    {
        $this->getObjectManagerId([])->shouldReturn('doctrine_phpcr.odm.document_manager');
    }

    public function it_returns_custom_object_manager_service_name()
    {
        $this->getObjectManagerId([
            'object_manager_name' => 'custom',
        ])->shouldReturn('doctrine_phpcr.odm.custom_document_manager');
    }

    public function it_returns_class_metadata_name()
    {
        $this->getClassMetadataClassName()->shouldReturn('\\Doctrine\\ODM\\PHPCR\\Mapping\\ClassMetadata');
    }

    public function it_returns_repository_class_parameter()
    {
        $this->getDriverRepositoryParameter()->shouldHaveParameterName('swp.phpcr_odm.repository.class');
    }

    public function it_is_supported()
    {
        $this->isSupported(PHPCRDriver::$type)->shouldReturn(true);
    }

    public function it_is_not_supported()
    {
        $this->isSupported('fake')->shouldReturn(false);
    }

    public function getMatchers()
    {
        return [
            'haveParameterName' => function (Parameter $parameter, $expectedName) {
                return (string) $parameter === $expectedName;
            },
        ];
    }
}
