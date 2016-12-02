<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\ContentBundle\Factory\PHPCR;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article;
use SWP\Bundle\ContentBundle\Factory\ORM\ArticleFactory;
use SWP\Bundle\ContentBundle\Factory\ArticleFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Component\Bridge\Model\Item;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

/**
 * @mixin ArticleFactory
 */
class ArticleFactorySpec extends ObjectBehavior
{
    public function let(FactoryInterface $factory, RouteProviderInterface $routeProvider)
    {
        $this->beConstructedWith($factory, $routeProvider);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ArticleFactory::class);
    }

    public function it_has_an_interface()
    {
        $this->shouldImplement(ArticleFactoryInterface::class);
    }

    public function it_creates_new_article_object(FactoryInterface $factory, ArticleInterface $article)
    {
        $factory->create()->willReturn($article);

        $this->create()->shouldReturn($article);
    }

    public function it_creates_article_from_package_and_fallbacks_slug_to_package_headline(
        FactoryInterface $factory,
        PackageInterface $package,
        Article $article,
        RouteInterface $route,
        RouteProviderInterface $routeProvider
    ) {
        $factory->create()->willReturn($article);

        $item = new Item();
        $item->setBody('some item body');
        $item->setType('text');
        $item->setDescription('item lead');

        $package->getHeadline()->shouldBeCalled()->willReturn('item headline');
        $package->getDescription()->shouldBeCalled()->willReturn('package lead');
        $package->getBody()->shouldBeCalled()->willReturn('some package body');
        $package->getItems()->shouldBeCalled()->willReturn(new ArrayCollection([$item]));
        $package->getLanguage()->shouldBeCalled()->willReturn('en');
        $package->getMetadata()->shouldBeCalled()->willReturn(['some' => 'meta']);
        $package->getSlugline()->shouldBeCalled();

        $article->setTitle('item headline')->shouldBeCalled();
        $article->setBody('some package body some item body')->shouldBeCalled();
        $article->setLead('package lead item lead')->shouldBeCalled();
        $article->setLocale('en')->shouldBeCalled();
        $article->setRoute($route)->shouldBeCalled();
        $article->setMetadata(['some' => 'meta'])->shouldBeCalled();
        $article->setSlug('item headline')->shouldNotBeCalled();

        $routeProvider->getRouteForArticle($article)->willReturn($route);

        $this->createFromPackage($package)->shouldReturn($article);
    }

    public function it_creates_article_from_package_and_sets_article_slug_from_package_slugline(
        FactoryInterface $factory,
        PackageInterface $package,
        Article $article,
        RouteInterface $route,
        RouteProviderInterface $routeProvider
    ) {
        $factory->create()->willReturn($article);

        $item = new Item();
        $item->setBody('some item body');
        $item->setType('text');
        $item->setDescription('item lead');

        $package->getHeadline()->shouldBeCalled()->willReturn('item headline');
        $package->getDescription()->shouldBeCalled()->willReturn('package lead');
        $package->getBody()->shouldBeCalled()->willReturn('some package body');
        $package->getItems()->shouldBeCalled()->willReturn(new ArrayCollection([$item]));
        $package->getLanguage()->shouldBeCalled()->willReturn('en');
        $package->getMetadata()->shouldBeCalled()->willReturn(['some' => 'meta']);
        $package->getSlugline()->shouldBeCalled()->willReturn('slugline');

        $article->setTitle('item headline')->shouldBeCalled();
        $article->setBody('some package body some item body')->shouldBeCalled();
        $article->setLead('package lead item lead')->shouldBeCalled();
        $article->setLocale('en')->shouldBeCalled();
        $article->setRoute($route)->shouldBeCalled();
        $article->setMetadata(['some' => 'meta'])->shouldBeCalled();
        $article->setSlug('slugline')->shouldBeCalled();

        $routeProvider->getRouteForArticle($article)->willReturn($route);

        $this->createFromPackage($package)->shouldReturn($article);
    }

    public function it_throws_an_exception_when_item_type_not_allowed(
        FactoryInterface $factory,
        PackageInterface $package,
        Article $article,
        RouteInterface $route
    ) {
        $factory->create()->willReturn($article);

        $item = new Item();
        $item->setBody('some item body');
        $item->setType('fake');

        $package->getHeadline()->shouldNotBeCalled();
        $package->getBody()->shouldBeCalled()->willReturn('some package body');
        $package->getItems()->shouldBeCalled()->willReturn(new ArrayCollection([$item]));
        $package->getLanguage()->shouldNotBeCalled();
        $package->getMetadata()->shouldNotBeCalled();

        $article->setTitle('item headline')->shouldNotBeCalled();
        $article->setBody('some package body some item body')->shouldNotBeCalled();
        $article->setLocale('en')->shouldNotBeCalled();
        $article->setRoute($route)->shouldNotBeCalled();
        $article->setMetadata(['some' => 'meta'])->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)
            ->duringCreateFromPackage($package);
    }
}
