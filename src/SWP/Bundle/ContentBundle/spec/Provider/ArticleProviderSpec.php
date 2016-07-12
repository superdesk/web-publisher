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
namespace spec\SWP\Bundle\ContentBundle\Provider;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Provider\ArticleProvider;
use SWP\Bundle\ContentBundle\Provider\ArticleProviderInterface;
use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;

/**
 * @mixin ArticleProvider
 */
class ArticleProviderSpec extends ObjectBehavior
{
    public function let(
        ArticleRepositoryInterface $articleRepository,
        TenantAwarePathBuilderInterface $pathBuilder
    ) {
        $this->beConstructedWith($articleRepository, $pathBuilder);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ArticleProvider::class);
    }

    public function it_implements_article_provider_interface()
    {
        $this->shouldImplement(ArticleProviderInterface::class);
    }

    public function it_gets_one_article_by_id(ArticleInterface $article, ArticleRepositoryInterface $articleRepository)
    {
        $articleRepository->findOneBy(['id' => 111])->willReturn($article);

        $this->getOneById(111)->shouldReturn($article);
    }

    public function it_gets_one_article_by_string_id(ArticleInterface $article, ArticleRepositoryInterface $articleRepository)
    {
        $articleRepository->findOneBy(['id' => '111'])->willReturn($article);

        $this->getOneById('111')->shouldReturn($article);
    }

    public function it_gets_one_article_by_slug(ArticleInterface $article, ArticleRepositoryInterface $articleRepository)
    {
        $articleRepository->findOneBySlug('slug')->willReturn($article);

        $this->getOneById('slug')->shouldReturn($article);
    }

    public function it_should_return_nothing(ArticleRepositoryInterface $articleRepository)
    {
        $articleRepository->findOneBySlug('slug')->willReturn(null);

        $this->getOneById('slug')->shouldBeNull();

        $articleRepository->findOneBy(['id' => '111'])->willReturn(null);

        $this->getOneById('111')->shouldBeNull();
    }

    public function it_gets_parent(
        TenantAwarePathBuilderInterface $pathBuilder,
        ArticleInterface $article,
        ArticleRepositoryInterface $articleRepository
    ) {
        $pathBuilder->build('content')->willReturn('/full/path/to/document');
        $articleRepository->find('/full/path/to/document')->willReturn($article);

        $this->getParent('content')->shouldReturn($article);
    }
}
