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

namespace SWP\Component\TemplatesSystem\Tests\Twig\Extension;

use Doctrine\Common\Cache\ArrayCache;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactory;
use SWP\Component\TemplatesSystem\Gimme\Loader\ArticleLoader;
use SWP\Component\TemplatesSystem\Twig\Extension\GimmeExtension;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\EventDispatcher\EventDispatcher;

class GimmeExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $context = new Context(new EventDispatcher(), new ArrayAdapter());
        $loader = new ArticleLoader(__DIR__.'/Node/Resources/meta', new MetaFactory($context));

        $gimmeExtension = new GimmeExtension($context, $loader);
        self::assertEquals($gimmeExtension->getContext(), $context);
        self::assertEquals($gimmeExtension->getLoader(), $loader);
        self::assertEquals($gimmeExtension->getGlobals(), ['gimme' => $context]);
    }
}
