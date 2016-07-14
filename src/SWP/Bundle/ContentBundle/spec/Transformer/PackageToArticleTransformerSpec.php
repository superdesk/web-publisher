<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\ContentBundle\Transformer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Factory\ArticleFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Transformer\PackageToArticleTransformer;
use SWP\Component\Bridge\Exception\MethodNotSupportedException;
use SWP\Component\Bridge\Exception\TransformationFailedException;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Bridge\Transformer\DataTransformerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @mixin PackageToArticleTransformer
 */
class PackageToArticleTransformerSpec extends ObjectBehavior
{
    public function let(ArticleFactoryInterface $articleFactory, EventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($articleFactory, $dispatcher);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PackageToArticleTransformer::class);
    }

    public function it_implements_transformer_interface()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    public function it_should_transform_package_to_article(
        PackageInterface $package,
        EventDispatcherInterface $dispatcher,
        ArticleFactoryInterface $articleFactory,
        ArticleInterface $article
    ) {
        $package->getHeadline()->willReturn('headline');
        $package->getSlugline()->willReturn('slug');
        $package->getLanguage()->willReturn('en');

        $article->getTitle()->willReturn('headline');
        $article->getSlug()->willReturn('slug');
        $article->getLocale()->willReturn('en');

        $articleFactory->createFromPackage($package)->willReturn($article);

        $dispatcher->dispatch(
            ArticleEvents::PRE_CREATE,
            Argument::type(ArticleEvent::class)
        )->shouldBeCalled();

        $this->transform($package)->shouldReturn($article);
    }

    public function it_should_throw_exception()
    {
        $this
            ->shouldThrow(TransformationFailedException::class)
            ->during('transform', [new \stdClass()]);
    }

    public function it_should_not_support_reverse_transform()
    {
        $this
            ->shouldThrow(MethodNotSupportedException::class)
            ->during('reverseTransform', [new \stdClass()]);
    }
}
