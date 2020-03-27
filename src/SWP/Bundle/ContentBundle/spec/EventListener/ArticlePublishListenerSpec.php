<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\ContentBundle\EventListener;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\EventListener\ArticlePublishListener;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Service\ArticleServiceInterface;

final class ArticlePublishListenerSpec extends ObjectBehavior
{
    public function let(ArticleServiceInterface $articleService)
    {
        $this->beConstructedWith($articleService);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ArticlePublishListener::class);
    }

    public function it_publishes_an_article_if_it_is_not_published(
        ArticleServiceInterface $articleService,
        ArticleEvent $event,
        ArticleInterface $article
    ) {
        $event->getArticle()->willReturn($article);
        $article->getExtra();
        $articleService->publish($article)->shouldBeCalled()->willReturn($article);
        $article->isPublished()->willReturn(false);

        $this->publish($event);
    }

    public function it_does_nothing_if_article_is_already_published(
        ArticleServiceInterface $articleService,
        ArticleEvent $event,
        ArticleInterface $article
    ) {
        $event->getArticle()->willReturn($article);
        $article->getExtra();
        $articleService->publish($article)->shouldNotBeCalled();
        $article->isPublished()->willReturn(true);

        $this->publish($event);
    }

    public function it_unpublishes_an_article_if_it_is_already_published(
        ArticleServiceInterface $articleService,
        ArticleEvent $event,
        ArticleInterface $article
    ) {
        $event->getArticle()->willReturn($article);
        $articleService->unpublish($article, ArticleInterface::STATUS_UNPUBLISHED)->shouldBeCalled()->willReturn($article);
        $article->isPublished()->willReturn(true);

        $this->unpublish($event);
    }

    public function it_doesnt_unpublishe_an_article_if_it_is_not_published(
        ArticleServiceInterface $articleService,
        ArticleEvent $event,
        ArticleInterface $article
    ) {
        $event->getArticle()->willReturn($article);
        $articleService->unpublish($article, ArticleInterface::STATUS_UNPUBLISHED)->shouldBeCalled()->shouldNotBeCalled();
        $article->isPublished()->willReturn(false);

        $this->unpublish($event);
    }

    public function it_cancels_an_article(
        ArticleServiceInterface $articleService,
        ArticleEvent $event,
        ArticleInterface $article
    ) {
        $event->getArticle()->willReturn($article);
        $articleService->unpublish($article, ArticleInterface::STATUS_CANCELED)->shouldBeCalled()->willReturn($article);
        $article->isPublished()->willReturn(true);

        $this->cancel($event);
    }
}
