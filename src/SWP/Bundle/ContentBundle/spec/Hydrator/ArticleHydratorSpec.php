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

namespace spec\SWP\Bundle\ContentBundle\Hydrator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Hydrator\ArticleHydrator;
use SWP\Bundle\ContentBundle\Hydrator\ArticleHydratorInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Component\Bridge\Model\Item;
use SWP\Component\Bridge\Model\PackageInterface;

/**
 * @mixin ArticleHydrator
 */
final class ArticleHydratorSpec extends ObjectBehavior
{
    public function let(RouteProviderInterface $routeProvider)
    {
        $this->beConstructedWith($routeProvider);
    }

    public function it_has_an_interface()
    {
        $this->shouldImplement(ArticleHydratorInterface::class);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ArticleHydrator::class);
    }

    public function it_hydrates_article_and_fallbacks_slug_to_package_headline(
        PackageInterface $package,
        ArticleInterface $article,
        RouteInterface $route,
        RouteProviderInterface $routeProvider
    ) {
        $item = new Item();
        $item->setBody('some item body');
        $item->setType('text');
        $item->setDescription('item lead');

        $package->getGuid()->shouldBeCalled()->willReturn('123guid223');
        $package->getHeadline()->shouldBeCalled()->willReturn('item headline');
        $package->getDescription()->shouldBeCalled()->willReturn('package lead');
        $package->getBody()->shouldBeCalled()->willReturn('some package body');
        $package->getByLine()->shouldBeCalled()->willReturn('Person');
        $package->getKeywords()->shouldBeCalled()->willReturn(['key1', 'key2']);
        $package->getItems()->shouldBeCalled()->willReturn(new ArrayCollection([$item]));
        $package->getSource()->willReturn('package_source');
        $package->getLanguage()->shouldBeCalled()->willReturn('en');
        $package->getMetadata()->shouldBeCalled()->willReturn(['some' => 'meta']);
        $package->getSlugline()->shouldBeCalled();

        $article->setCode('123guid223')->shouldBeCalled();
        $article->setTitle('item headline')->shouldBeCalled();
        $article->setBody('some package body some item body')->shouldBeCalled();
        $article->setLead('package lead')->shouldBeCalled();
        $article->setLocale('en')->shouldBeCalled();
        $article->setRoute($route)->shouldBeCalled();
        $article->setMetadata(['some' => 'meta'])->shouldBeCalled();
        $article->setKeywords(['key1', 'key2'])->shouldBeCalled();
        $article->setSlug('item headline')->shouldNotBeCalled();
        $article->setSource('package_source')->shouldBeCalled();

        $routeProvider->getRouteForArticle($article)->willReturn($route);

        $this->hydrate($article, $package)->shouldReturn($article);
    }

    public function it_hydrates_article_from_package_and_sets_article_slug_from_package_slugline(
        PackageInterface $package,
        ArticleInterface $article,
        RouteInterface $route,
        RouteProviderInterface $routeProvider
    ) {
        $item = new Item();
        $item->setBody('some item body');
        $item->setType('text');
        $item->setDescription('item lead');

        $package->getGuid()->shouldBeCalled()->willReturn('123guid223');
        $package->getHeadline()->shouldBeCalled()->willReturn('item headline');
        $package->getDescription()->shouldBeCalled()->willReturn('package lead');
        $package->getBody()->shouldBeCalled()->willReturn('some package body');
        $package->getByLine()->shouldBeCalled()->willReturn('Person');
        $package->getKeywords()->shouldBeCalled()->willReturn(['key1', 'key2']);
        $package->getItems()->shouldBeCalled()->willReturn(new ArrayCollection([$item]));
        $package->getSource()->willReturn('package_source');
        $package->getLanguage()->shouldBeCalled()->willReturn('en');
        $package->getMetadata()->shouldBeCalled()->willReturn(['some' => 'meta']);
        $package->getSlugline()->shouldBeCalled()->willReturn('slugline');

        $article->setCode('123guid223')->shouldBeCalled();
        $article->setTitle('item headline')->shouldBeCalled();
        $article->setBody('some package body some item body')->shouldBeCalled();
        $article->setLead('package lead')->shouldBeCalled();
        $article->setLocale('en')->shouldBeCalled();
        $article->setRoute($route)->shouldBeCalled();
        $article->setMetadata(['some' => 'meta'])->shouldBeCalled();
        $article->setSlug('slugline')->shouldBeCalled();
        $article->setKeywords(['key1', 'key2'])->shouldBeCalled();
        $article->setSource('package_source')->shouldBeCalled();

        $routeProvider->getRouteForArticle($article)->willReturn($route);

        $this->hydrate($article, $package)->shouldReturn($article);
    }

    public function it_throws_an_exception_when_item_type_not_allowed(
        PackageInterface $package,
        ArticleInterface $article,
        RouteInterface $route
    ) {
        $item = new Item();
        $item->setBody('some item body');
        $item->setType('fake');

        $package->getGuid()->shouldNotBeCalled();
        $package->getHeadline()->shouldNotBeCalled();
        $package->getBody()->shouldNotBeCalled();
        $package->getKeywords()->shouldNotBeCalled();
        $package->getItems()->shouldBeCalled()->willReturn(new ArrayCollection([$item]));
        $package->getLanguage()->shouldNotBeCalled();
        $package->getMetadata()->shouldNotBeCalled();

        $article->setTitle('item headline')->shouldNotBeCalled();
        $article->setBody('some package body some item body')->shouldNotBeCalled();
        $article->setLocale('en')->shouldNotBeCalled();
        $article->setRoute($route)->shouldNotBeCalled();
        $article->setMetadata(['some' => 'meta'])->shouldNotBeCalled();
        $article->setKeywords(['key1', 'key2'])->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)
            ->duringHydrate($article, $package);
    }
}
