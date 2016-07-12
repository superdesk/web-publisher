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
namespace spec\SWP\Bundle\ContentBundle\Event;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @mixin ArticleEvent
 */
class ArticleEventSpec extends ObjectBehavior
{
    public function let(ArticleInterface $article)
    {
        $this->beConstructedWith($article);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ArticleEvent::class);
        $this->shouldHaveType(Event::class);
    }

    public function it_has_an_article(ArticleInterface $article)
    {
        $this->getArticle()->shouldReturn($article);
    }
}
