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
use SWP\Component\TemplatesSystem\Gimme\Loader\ChainLoader;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ChainLoaderTest extends \PHPUnit\Framework\TestCase
{
    private $loader;

    public function setUp(): void
    {
        $this->loader = new ChainLoader();
    }

    public function testAddingNewLoader()
    {
        $articleLoader = new ArticleLoader(
            __DIR__.'/../../Twig/Node',
            new MetaFactory(new Context(new EventDispatcher(), new ArrayAdapter()))
        );
        $this->loader->addLoader($articleLoader);

        $this->assertTrue($this->loader->isSupported('article'));
        unset($this->loader);
        unset($articleLoader);
    }
}
