<?php

/**
 * This file is part of the Superdesk Web Publisher Updater Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\UpdaterBundle\Tests\DependencyInjection;

use SWP\UpdaterBundle\DependencyInjection\Compiler\MonologCompilerPass;
use Symfony\Component\DependencyInjection\Reference;

class MonologCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $definition;
    private $pass;

    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $this->definition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $this->pass = new MonologCompilerPass();
    }

    /**
     * @covers SWP\UpdaterBundle\DependencyInjection\Compiler\MonologCompilerPass::process
     */
    public function testProcess()
    {
        $this->container->expects($this->once())
            ->method('hasParameter')
            ->with('swp_updater.monolog_channel')
            ->will($this->returnValue(true));

        $this->container->expects($this->once())
            ->method('getParameter')
            ->with('kernel.bundles')
            ->will($this->returnValue(array(
                'MonologBundle' => true,
            )));

        $this->container->expects($this->once())
            ->method('getDefinition')
            ->with('swp_updater.manager')
            ->will($this->returnValue($this->definition));

        $this->definition->expects($this->once())
            ->method('addArgument')
            ->with(new Reference('monolog.logger.updater'));

        $this->pass->process($this->container);
    }

    /**
     * @covers SWP\UpdaterBundle\DependencyInjection\Compiler\MonologCompilerPass::process
     */
    public function testNoConfig()
    {
          $this->container->expects($this->once())
            ->method('hasParameter')
            ->with('swp_updater.monolog_channel')
            ->will($this->returnValue(false));

        $this->pass->process($this->container);
    }

    /**
     * @expectedException Symfony\Component\DependencyInjection\Exception\RuntimeException
     */
    public function testNoBundle()
    {
        $this->container->expects($this->once())
            ->method('hasParameter')
            ->with('swp_updater.monolog_channel')
            ->will($this->returnValue(true));

        $this->container->expects($this->once())
            ->method('getParameter')
            ->with('kernel.bundles')
            ->will($this->returnValue(array()));

        $this->pass->process($this->container);
    }
}
