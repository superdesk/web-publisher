<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\CoreBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\EventSubscriber\ArticleTenantSubscriber;
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;

final class ArticleTenantSubscriberSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ArticleTenantSubscriber::class);
    }

    public function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement(EventSubscriber::class);
    }

    public function it_subscribes_to_an_event()
    {
        $this->getSubscribedEvents()->shouldReturn([Events::prePersist]);
    }

    public function it_should_skip_when_tenant_is_already_not_set_on_article_object(
        LifecycleEventArgs $event
    ) {
        $article = new Article();
        $article->setTitle('Hello World');
        $article->setSlug('hello-world');
        $article->setTenantCode(null);

        $event->getEntity()->willReturn($article);

        $this->prePersist($event);
    }

    public function it_unsets_the_article_tenant_code_when_it_is_already_set(
        LifecycleEventArgs $event,
        ArticleInterface $article
    ) {
        $article->getTitle()->willReturn('Hello World');
        $article->getSlug()->willReturn('hello-world');
        $article->getTenantCode()->willReturn('123456');

        $event->getEntity()->willReturn($article);
        $article->setTenantCode(null)->shouldBeCalled();

        $this->prePersist($event);
    }

    public function it_unsets_tenant_code_only_for_article_objects(
        ArticleInterface $article,
        LifecycleEventArgs $event
    ) {
        $item = new \stdClass();
        $event->getEntity()->willReturn($item);
        $article->getTenantCode()->shouldNotBeCalled();

        $this->prePersist($event);
    }
}
