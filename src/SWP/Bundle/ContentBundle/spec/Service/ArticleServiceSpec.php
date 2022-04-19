<?php

namespace spec\SWP\Bundle\ContentBundle\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Event\RouteEvent;
use SWP\Bundle\ContentBundle\Service\ArticleService;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ArticleServiceSpec extends ObjectBehavior
{
    public function let(EventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($dispatcher);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ArticleService::class);
    }

    public function it_should_publish_new_article(ArticleInterface $article, EventDispatcherInterface $dispatcher)
    {
        $article->setStatus(ArticleInterface::STATUS_PUBLISHED)->shouldBeCalled();
        $article->getPublishedAt()->willReturn(null);
        $article->setPublishedAt(Argument::type('\DateTime'))->shouldBeCalled();
        $article->setPublishable(true)->shouldBeCalled();
        $article->getPublishStartDate()->shouldBeCalled();
        $article->getPublishEndDate()->shouldBeCalled();

        $dispatcher->dispatch(Argument::type(ArticleEvent::class), 'swp.article.published')->shouldBeCalled();

        $this->publish($article)->shouldReturn($article);
    }

    public function it_should_throw_exception_on_unpublishable_article(ArticleInterface $article)
    {
        $date = new \DateTime();
        $date->modify('-10 years');
        $article->getPublishEndDate()->willReturn($date);
        $article->setStatus(ArticleInterface::STATUS_PUBLISHED)->shouldNotBeCalled();
        $article->setPublishedAt(Argument::any())->shouldNotBeCalled();
        $article->setPublishable(true)->shouldNotBeCalled();
        $article->getPublishStartDate()->shouldBeCalled();
        $article->getPublishEndDate()->shouldBeCalled();

        $this
            ->shouldThrow(\Exception::class)
            ->during('publish', [$article]);
    }

    public function it_should_unpublish_published_article(ArticleInterface $article,  EventDispatcherInterface $dispatcher)
    {
        $dispatcher->dispatch(
            Argument::any(),
            Argument::any()
        )->willReturn(Argument::type(ArticleEvent::class));

        $article->setStatus(ArticleInterface::STATUS_UNPUBLISHED)->shouldBeCalled();
        $article->setPublishedAt(Argument::type('\DateTime'))->shouldNotBeCalled();
        $article->setPublishable(false)->shouldBeCalled();
        $article->getPublishStartDate()->shouldBeCalled();
        $article->getPublishEndDate()->shouldBeCalled();

        $this->unpublish($article, ArticleInterface::STATUS_UNPUBLISHED)->shouldReturn($article);
    }
}
