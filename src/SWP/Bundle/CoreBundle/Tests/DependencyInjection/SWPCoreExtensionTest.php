<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use SWP\Bundle\CoreBundle\DependencyInjection\SWPCoreExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SWPCoreExtensionTest extends TestCase
{
    private $extension;

    private $container;

    public function setUp(): void
    {
        $this->extension = new SWPCoreExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    public function testloadConfiguration()
    {
        $this->extension->load([], $this->container);
        $this->assertTrue($this->container->hasExtension('swp_core'));
    }
}
