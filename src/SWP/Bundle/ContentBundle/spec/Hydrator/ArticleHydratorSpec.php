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
use SWP\Bundle\ContentBundle\Model\ArticleAuthor;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\Metadata;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Service\ArticleKeywordAdderInterface;
use SWP\Bundle\ContentBundle\Service\ArticleSourcesAdderInterface;
use SWP\Component\Bridge\Model\Item;
use SWP\Component\Bridge\Model\PackageInterface;

/**
 * @mixin ArticleHydrator
 */
final class ArticleHydratorSpec extends ObjectBehavior
{
    public function let(ArticleSourcesAdderInterface $articleSourcesAdder, ArticleKeywordAdderInterface $articleKeywordAdder)
    {
        $this->beConstructedWith($articleSourcesAdder, $articleKeywordAdder);
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
        ArticleSourcesAdderInterface $articleSourcesAdder,
        ArticleKeywordAdderInterface $articleKeywordAdder
    ) {
        $item = new Item();
        $item->setBody('some item body');
        $item->setType('text');
        $item->setDescription('item lead');
        $item->setSource('item_source');

        $author = new ArticleAuthor();
        $author->setName('Test Person');
        $author->setRole('Writer');
        $authors = new ArrayCollection([$author]);

        $extra = ['custom-field' => 'hello'];

        $package->getGuid()->shouldBeCalled()->willReturn('123guid223');
        $package->getHeadline()->shouldBeCalled()->willReturn('item headline');
        $package->getDescription()->shouldBeCalled()->willReturn('package lead');
        $package->getBody()->shouldBeCalled()->willReturn('some package body');
        $package->getKeywords()->shouldBeCalled()->willReturn(['key1', 'key2']);
        $package->getItems()->willReturn(new ArrayCollection([$item]));
        $package->getSource()->willReturn('package_source');
        $package->getLanguage()->shouldBeCalled()->willReturn('en');
        $package->getMetadata()->shouldBeCalled()->willReturn(['some' => 'meta']);
        $package->getSlugline()->shouldBeCalled();
        $package->getAuthors()->willReturn($authors);
        $package->getExtra()->willReturn($extra);
        $package->getSubjects()->willReturn([]);
        $package->getServices()->willReturn([]);
        $package->getPlaces()->willReturn([]);
        $package->getProfile()->willReturn('profile');
        $package->getUrgency()->willReturn(0);
        $package->getPriority()->willReturn(1);
        $package->getEdNote()->willReturn(null);
        $package->getGenre()->willReturn(null);

        $article->setExtra($extra)->shouldBeCalled();
        $article->getData()->willReturn(null);
        $metadata = new Metadata();
        $metadata->setGuid('123guid223');
        $metadata->setLanguage('en');
        $metadata->setPriority(1);
        $metadata->setProfile('profile');
        $article->setData($metadata)->shouldBeCalled();
        $article->setAuthors($authors)->shouldBeCalled();
        $article->setCode('123guid223')->shouldBeCalled();
        $article->setTitle('item headline')->shouldBeCalled();
        $article->setBody('some package body')->shouldBeCalled();
        $article->setLead('package lead')->shouldBeCalled();
        $article->setLocale('en')->shouldBeCalled();
        $article->setMetadata(['some' => 'meta'])->shouldBeCalled();
        $article->getKeywords()->willReturn(new ArrayCollection());
        $articleKeywordAdder->add($article, 'key1')->shouldBeCalled();
        $articleKeywordAdder->add($article, 'key2')->shouldBeCalled();
        $article->setSlug('item headline')->shouldNotBeCalled();
        $articleSourcesAdder->add($article, 'package_source')->shouldBeCalled();

        $this->hydrate($article, $package)->shouldReturn($article);
    }

    public function it_hydrates_article_from_package_and_sets_article_slug_from_package_slugline(
        PackageInterface $package,
        ArticleInterface $article,
        RouteInterface $route,
        ArticleSourcesAdderInterface $articleSourcesAdder,
        ArticleKeywordAdderInterface $articleKeywordAdder
    ) {
        $item = new Item();
        $item->setBody('some item body');
        $item->setType('text');
        $item->setDescription('item lead');

        $author = new ArticleAuthor();
        $author->setName('Test Person');
        $author->setRole('Writer');
        $authors = new ArrayCollection([$author]);

        $extra = ['custom-field' => 'hello'];

        $package->getGuid()->shouldBeCalled()->willReturn('123guid223');
        $package->getHeadline()->shouldBeCalled()->willReturn('item headline');
        $package->getDescription()->shouldBeCalled()->willReturn('package lead');
        $package->getBody()->shouldBeCalled()->willReturn('some package body');
        $package->getKeywords()->shouldBeCalled()->willReturn(['key1', 'key2']);
        $package->getItems()->willReturn(new ArrayCollection([$item]));
        $package->getSource()->willReturn('package_source');
        $package->getLanguage()->shouldBeCalled()->willReturn('en');
        $package->getMetadata()->shouldBeCalled()->willReturn(['some' => 'meta']);
        $package->getSlugline()->shouldBeCalled()->willReturn('slugline');
        $package->getAuthors()->willReturn($authors);
        $package->getExtra()->willReturn($extra);
        $package->getSubjects()->willReturn([]);
        $package->getServices()->willReturn([]);
        $package->getPlaces()->willReturn([]);
        $package->getProfile()->willReturn('profile');
        $package->getUrgency()->willReturn(0);
        $package->getPriority()->willReturn(1);
        $package->getEdNote()->willReturn(null);
        $package->getGenre()->willReturn(null);

        $article->getData()->willReturn(null);
        $metadata = new Metadata();
        $metadata->setGuid('123guid223');
        $metadata->setLanguage('en');
        $metadata->setPriority(1);
        $metadata->setProfile('profile');

        $article->setData($metadata)->shouldBeCalled();
        $article->setExtra($extra)->shouldBeCalled();
        $article->setAuthors($authors)->shouldBeCalled();
        $article->getSlug()->shouldBeCalled();
        $article->setCode('123guid223')->shouldBeCalled();
        $article->setTitle('item headline')->shouldBeCalled();
        $article->setBody('some package body')->shouldBeCalled();
        $article->setLead('package lead')->shouldBeCalled();
        $article->setLocale('en')->shouldBeCalled();
        $article->setMetadata(['some' => 'meta'])->shouldBeCalled();
        $article->setSlug('slugline')->shouldBeCalled();
        $article->getKeywords()->willReturn(new ArrayCollection());
        $articleKeywordAdder->add($article, 'key1')->shouldBeCalled();
        $articleKeywordAdder->add($article, 'key2')->shouldBeCalled();
        $articleSourcesAdder->add($article, 'package_source')->shouldBeCalled();

        $this->hydrate($article, $package)->shouldReturn($article);
    }
}
