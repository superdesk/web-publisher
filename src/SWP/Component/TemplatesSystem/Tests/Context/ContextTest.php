<?php
/**
 * Created by PhpStorm.
 * User: pawelmikolajczuk
 * Date: 24.08.2016
 * Time: 16:56
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
