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

namespace spec\SWP\Bundle\ContentBundle\Factory\ORM;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Factory\ArticleFactoryInterface;
use SWP\Bundle\ContentBundle\Factory\ORM\ArticleFactory;
use SWP\Bundle\ContentBundle\Hydrator\ArticleHydratorInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

/**
 * @mixin ArticleFactory
 */
final class ArticleFactorySpec extends ObjectBehavior
{
    public function let(FactoryInterface $factory, ArticleHydratorInterface $articleHydrator)
    {
        $this->beConstructedWith($factory, $articleHydrator);
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

    public function it_creates_a_new_article_based_on_package(
        FactoryInterface $factory,
        PackageInterface $package,
        ArticleInterface $article,
        ArticleHydratorInterface $articleHydrator
    ) {
        $factory->create()->willReturn($article);

        $articleHydrator->hydrate($article, $package)->willReturn($article);

        $this->createFromPackage($package)->shouldReturn($article);
    }
}
