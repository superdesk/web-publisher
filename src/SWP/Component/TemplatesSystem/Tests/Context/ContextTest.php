<?php
/**
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\TemplatesSystem\Tests\Context;

use Doctrine\Common\Cache\ArrayCache;
use SWP\Bundle\ContentBundle\Model\Article;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Context
     */
    protected $context;

    public function testInitialization()
    {
        $this->context = new Context(new ArrayCache());
        self::assertInstanceOf(Context::class, $this->context);
    }

    public function testInitializationWithDefaultCOnfigurations()
    {
        $this->context = new Context(new ArrayCache(), __DIR__.'/../../spec/Gimme/Meta/Resources/meta/');
        self::assertCount(1, $this->context->getAvailableConfigs());
    }

    public function testAddingNewConfiguration()
    {
        $this->context = new Context(new ArrayCache());
        $configuration = $this->context->addNewConfig(__DIR__.'/../../spec/Gimme/Meta/Resources/meta/article.yml');

        self::assertCount(1, $this->context->getAvailableConfigs());
        self::assertEquals($this->context->getAvailableConfigs()[ArticleInterface::class], $configuration);
    }

    public function testAddingNewMeta()
    {
        $this->context = new Context(new ArrayCache(), __DIR__.'/../../spec/Gimme/Meta/Resources/meta/');
        $meta = $this->context->getMetaForValue(new Article());
        $this->context->registerMeta($meta);

        self::assertInstanceOf(Meta::class, $this->context->article);
        self::assertCount(1, $this->context->getRegisteredMeta());
    }

    public function testIfIsSupported()
    {
        $this->context = new Context(new ArrayCache());
        self::assertFalse($this->context->isSupported(new Article()));

        $this->context->addNewConfig(__DIR__.'/../../spec/Gimme/Meta/Resources/meta/article.yml');
        self::assertTrue($this->context->isSupported(new Article()));
    }
}
