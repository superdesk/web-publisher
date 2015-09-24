<?php

/**
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\FixturesBundleBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AbstractFixtureTest extends KernelTestCase
{
    private $container;
    private $manager;

    public function setUp()
    {
        $this->manager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $kernel = $this->createKernel();
        $kernel->boot();
        $this->container = $kernel->getContainer();
    }

    /**
     * @covers SWP\FixturesBundle\AbstractFixture::loadFixtures
     */
    public function testLoadFixturesMethod()
    {
        $path = '@SWPFixturesBundle/Tests/Fixtures/test.yml';
        $stub = $this->createStub($path);
        $this->assertNull($stub->loadFixtures($path, $this->manager));
    }

    public function testLoadFixturesMethodArray()
    {
        $paths = ['@SWPFixturesBundle/Tests/Fixtures/test.yml'];
        $stub = $this->createStub($paths);
        $this->assertNull($stub->loadFixtures($paths, $this->manager));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadFixturesMethodException()
    {
        $paths = ['@SWPFixturesBundle/Tests/fake_path/test.yml'];
        $stub = $this->createStub($paths);
        $this->assertNull($stub->loadFixtures($paths, $this->manager));
    }

    private function createStub($paths)
    {
        $stub = $this->getMockForAbstractClass('SWP\FixturesBundle\AbstractFixture');
        $stub->expects($this->any())
             ->method('loadFixtures')
             ->with($paths, $this->manager)
             ->will($this->returnValue(null));

        $stub->setContainer($this->container);

        return $stub;
    }
}
