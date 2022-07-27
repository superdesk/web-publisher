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

namespace SWP\Component\TemplatesSystem\Tests\Gimme\Loader;

use Doctrine\Common\Cache\ArrayCache;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactory;
use SWP\Component\TemplatesSystem\Gimme\Loader\ArticleLoader;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ArticleLoaderTest extends \PHPUnit\Framework\TestCase
{
    public function testLoadingCollection()
    {
        $context = new Context(new EventDispatcher(), new ArrayAdapter());
        $metaFactory = new MetaFactory($context);
        $articleLoader = new ArticleLoader(__DIR__.'/../../Twig/Node', $metaFactory);

        $result = $articleLoader->load('articles', [], [], LoaderInterface::COLLECTION);
        self::assertInstanceOf(MetaCollection::class, $result);
        self::assertCount(2, $result);
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
