<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use SWP\Bundle\ContentBundle\DependencyInjection\SWPContentExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SWPContentExtensionTest extends TestCase
{
    private $extension;

    private $container;

    public function setUp(): void
    {
        $this->extension = new SWPContentExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    public function testloadConfiguration()
    {
        $this->extension->load([], $this->container);
        $this->assertTrue($this->container->hasExtension('swp_content'));
        $this->assertTrue($this->container->has('swp_content_bundle.factory.knp_paginator_representation'));
        $this->assertTrue($this->container->has('swp_content_bundle.listener.link_request'));
        $this->assertTrue($this->container->has('swp_template_engine.loader.article'));
    }

    protected function tearDown(): void
    {
        $reflection = new \ReflectionObject($this);
        foreach ($reflection->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }
    }
}
