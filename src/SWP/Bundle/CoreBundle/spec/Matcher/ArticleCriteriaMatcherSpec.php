<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\CoreBundle\Matcher;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Matcher\ArticleCriteriaMatcher;
use SWP\Bundle\CoreBundle\Matcher\ArticleCriteriaMatcherInterface;
use SWP\Component\Common\Criteria\Criteria;

final class ArticleCriteriaMatcherSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ArticleCriteriaMatcher::class);
    }

    public function it_should_implement_interface()
    {
        $this->shouldImplement(ArticleCriteriaMatcherInterface::class);
    }

    public function it_should_match_article_criteria(
        ArticleInterface $article,
        RouteInterface $route
    ) {
        $route->getId()->willReturn(1);
        $article->getRoute()->willReturn($route);
        $criteria = new Criteria(['route' => [1]]);

        $this->match($article, $criteria)->shouldReturn(true);

        $article->getMetadataByKey('byline')->willReturn('Doe');

        $criteria = new Criteria(['author' => ['Doe']]);
        $this->match($article, $criteria)->shouldReturn(true);

        $article->getPublishedAt()->willReturn(new \DateTime('2017-01-03'));
        $criteria = new Criteria(['publishedAt' => '2017-01-03']);

        $this->match($article, $criteria)->shouldReturn(true);

        $article->getPublishedAt()->willReturn(new \DateTime('2017-01-03'));
        $criteria = new Criteria(['publishedBefore' => '2017-01-04']);

        $this->match($article, $criteria)->shouldReturn(true);

        $article->getPublishedAt()->willReturn(new \DateTime('2017-01-03'));
        $criteria = new Criteria(['publishedAfter' => '2017-01-02']);

        $this->match($article, $criteria)->shouldReturn(true);
    }

    public function it_should_not_match_article_criteria(
        ArticleInterface $article,
        RouteInterface $route
    ) {
        $route->getId()->willReturn(1);
        $article->getRoute()->willReturn($route);
        $article->getMetadataByKey('byline')->willReturn('Doe');
        $criteria = new Criteria(['route' => [1, 2]]);

        $this->match($article, $criteria)->shouldReturn(false);

        $criteria = new Criteria(['author' => ['fake']]);
        $this->match($article, $criteria)->shouldReturn(false);

        $article->getPublishedAt()->willReturn(new \DateTime('2017-01-03'));
        $criteria = new Criteria(['publishedAt' => '2017-01-04']);

        $this->match($article, $criteria)->shouldReturn(false);

        $article->getPublishedAt()->willReturn(new \DateTime('2017-01-03'));
        $criteria = new Criteria(['publishedBefore' => '2017-01-02']);

        $this->match($article, $criteria)->shouldReturn(false);

        $article->getPublishedAt()->willReturn(new \DateTime('2017-01-03'));
        $criteria = new Criteria(['publishedAfter' => '2017-01-04']);

        $this->match($article, $criteria)->shouldReturn(false);
    }
}
