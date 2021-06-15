<?php
/*
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\TemplatesSystem\Tests\Gimme\Context;

use Doctrine\Common\Cache\ArrayCache;
use SWP\Component\TemplatesSystem\Tests\Article;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ContextTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Context
     */
    protected $context;

    public function testInitialization()
    {
        $this->context = new Context(new EventDispatcher(), new ArrayCache());
        self::assertInstanceOf(Context::class, $this->context);
    }

    public function testInitializationWithDefaultConfigurations()
    {
        $this->context = new Context(new EventDispatcher(), new ArrayCache(), __DIR__.'/../../Twig/Node/Resources/meta/');
        self::assertCount(1, $this->context->getAvailableConfigs());
    }

    public function testAddingNewConfiguration()
    {
        $this->context = new Context(new EventDispatcher(), new ArrayCache());
        $configuration = $this->context->addNewConfig(__DIR__.'/../../Twig/Node/Resources/meta/article.yml');

        self::assertCount(1, $this->context->getAvailableConfigs());
        self::assertEquals($this->context->getAvailableConfigs()[Article::class], $configuration);
    }

    public function testAddingNewMeta()
    {
        $this->context = new Context(new EventDispatcher(), new ArrayCache(), __DIR__.'/../../Twig/Node/Resources/meta/');
        $meta = $this->context->getMetaForValue(new Article());
        $this->context->registerMeta($meta);

        self::assertInstanceOf(Meta::class, $this->context->article);
        self::assertCount(1, $this->context->getRegisteredMeta());
    }

    public function testIfIsSupported()
    {
        $this->context = new Context(new EventDispatcher(), new ArrayCache());
        self::assertFalse($this->context->isSupported(new Article()));

        $this->context->addNewConfig(__DIR__.'/../../Twig/Node/Resources/meta/article.yml');
        self::assertTrue($this->context->isSupported(new Article()));
    }

    public function testTemporaryUnsetAndRestore()
    {
        $this->context = new Context(new EventDispatcher(), new ArrayCache(), __DIR__.'/../../Twig/Node/Resources/meta/');
        $meta = $this->context->getMetaForValue(new Article());
        $this->context->registerMeta($meta);

        self::assertInstanceOf(Meta::class, $this->context->article);
        self::assertCount(1, $this->context->getRegisteredMeta());

        $key = $this->context->temporaryUnset(['article']);
        self::assertTrue(is_string($key));
        self::assertTrue(!isset($this->context['article']));

        $this->context->restoreTemporaryUnset($key);
        self::assertTrue(isset($this->context->article));
        self::assertInstanceOf(Meta::class, $this->context->article);
        self::assertCount(1, $this->context->getRegisteredMeta());
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
