<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge for the Content API.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\BridgeBundle\Tests\DependencyInjection;

use SWP\Bundle\BridgeBundle\DependencyInjection\SWPBridgeExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class SWPBridgeExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers SWP\Bundle\BridgeBundle\SWPBridgeBundle
     * @covers SWP\Bundle\BridgeBundle\DependencyInjection\SWPBridgeExtension::load
     * @covers SWP\Bundle\BridgeBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testLoad()
    {
        $data = [
            'swp_bridge.api.host'       => 'example.com',
            'swp_bridge.api.port'       => 8000,
            'swp_bridge.api.protocol'   => 'http',
            'swp_bridge.auth.client_id' => 'my_client_id',
            'swp_bridge.auth.username'  => 'my_username',
            'swp_bridge.auth.password'  => 'my_password',
            'swp_bridge.options'        => ['curl' => 'dummy'],
        ];

        $container = $this->createContainer($data);
        $loader = $this->createLoader();
        $config = $this->getConfig();

        $loader->load([$config], $container);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $container->getParameter($key));
        }
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadWhenHostIsRequiredAndCannotBeEmpty()
    {
        $container = $this->createContainer();
        $loader = $this->createLoader();

        $config = [
            'swp_bridge.api.host' => '',
        ];

        $loader->load([$config], $container);
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadWhenClientIdIsRequiredAndCannotBeEmpty()
    {
        $container = $this->createContainer();
        $loader = $this->createLoader();

        $config = [
            'swp_bridge.auth.client_id' => '',
        ];

        $loader->load([$config], $container);
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadWhenUsernameIsRequiredAndCannotBeEmpty()
    {
        $container = $this->createContainer();
        $loader = $this->createLoader();

        $config = [
            'swp_bridge.auth.username' => '',
        ];

        $loader->load([$config], $container);
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadWhenPasswordIsRequiredAndCannotBeEmpty()
    {
        $container = $this->createContainer();
        $loader = $this->createLoader();

        $config = [
            'swp_bridge.auth.password' => '',
        ];

        $loader->load([$config], $container);
    }

    protected function createLoader()
    {
        return new SWPBridgeExtension();
    }

    protected function getConfig()
    {
        return [
            'api' => [
                'host'     => 'example.com',
                'port'     => 8000,
                'protocol' => 'http',
            ],
            'auth' => [
                'client_id' => 'my_client_id',
                'username'  => 'my_username',
                'password'  => 'my_password',
            ],
            'options' => [
                'curl' => 'dummy',
            ],
        ];
    }

    protected function createContainer(array $data = [])
    {
        return new ContainerBuilder(new ParameterBag($data));
    }
}
