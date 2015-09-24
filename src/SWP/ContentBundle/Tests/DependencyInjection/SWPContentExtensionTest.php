<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\ContentBundle\Tests\DependencyInjection;

use SWP\ContentBundle\DependencyInjection\SWPContentExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SWPContentExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;
    private $container;

    public function setUp()
    {
        $this->extension = new SWPContentExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    public function testloadConfiguration()
    {
        $this->extension->load([], $this->container);

        $this->assertTrue($this->container->hasExtension('swp_content'));
        $this->assertTrue($this->container->has('swp_renderer.phpcr.initializer'));
        $this->assertTrue($this->container->has('swp_template_engine.loader.article'));
    }
}
