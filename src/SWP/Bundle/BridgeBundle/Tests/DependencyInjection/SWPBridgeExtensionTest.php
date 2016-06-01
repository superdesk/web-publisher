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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use SWP\Bundle\BridgeBundle\DependencyInjection\SWPBridgeExtension;
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
        $data = array(
            'swp_bridge.api.host' => 'example.com',
            'swp_bridge.api.port' => 8000,
            'swp_bridge.api.protocol' => 'http',
            'swp_bridge.auth.client_id' => 'my_client_id',
            'swp_bridge.auth.username' => 'my_username',
            'swp_bridge.auth.password' => 'my_password',
            'swp_bridge.options' => array('curl' => 'dummy')
        );

        $container = $this->createContainer($data);
        $loader = $this->createLoader();
        $config = $this->getConfig();

        $loader->load(array($config), $container);

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

        $config = array(
            'swp_bridge.api.host' => '',
        );

        $loader->load(array($config), $container);
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadWhenClientIdIsRequiredAndCannotBeEmpty()
    {
        $container = $this->createContainer();
        $loader = $this->createLoader();

        $config = array(
            'swp_bridge.auth.client_id' => '',
        );

        $loader->load(array($config), $container);
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadWhenUsernameIsRequiredAndCannotBeEmpty()
    {
        $container = $this->createContainer();
        $loader = $this->createLoader();

        $config = array(
            'swp_bridge.auth.username' => '',
        );

        $loader->load(array($config), $container);
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadWhenPasswordIsRequiredAndCannotBeEmpty()
    {
        $container = $this->createContainer();
        $loader = $this->createLoader();

        $config = array(
            'swp_bridge.auth.password' => '',
        );

        $loader->load(array($config), $container);
    }

    protected function createLoader()
    {
        return new SWPBridgeExtension();
    }

    protected function getConfig()
    {
        return array(
            'api' => array(
                'host' => 'example.com',
                'port' => 8000,
                'protocol' => 'http'
            ),
            'auth' => array(
                'client_id' => 'my_client_id',
                'username' => 'my_username',
                'password' => 'my_password'
            ),
            'options' => array(
                'curl' => 'dummy'
            )
        );
    }

    protected function createContainer(array $data = array())
    {
        return new ContainerBuilder(new ParameterBag($data));
    }
}
