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

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\CoreBundle\EventSubscriber\ArticlePublishSubscriber;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\Tenant;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ArticlePublishSubscriberSpec extends ObjectBehavior
{
    public function let(ContainerInterface $container)
    {
        $this->beConstructedWith($container);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ArticlePublishSubscriber::class);
    }

    public function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    public function it_subscribes_to_an_event()
    {
        $this->getSubscribedEvents()->shouldReturn([ArticleEvents::POST_PUBLISH => 'onPublish']);
    }

    public function it_sets_the_tenant_code_when_it_is_not_set_on_article_post_publish_event(
        ArticleEvent $event,
        ArticleInterface $article,
        TenantContextInterface $tenantContext,
        ContainerInterface $container
    ) {
        $article->getTenantCode()->willReturn(null);

        $tenant = new Tenant();
        $tenant->setSubdomain('example.com');
        $tenant->setName('Example');
        $tenant->setCode('123456');

        $event->getArticle()->willReturn($article);

        $tenantContext->getTenant()->shouldBeCalled()->willReturn($tenant);
        $container->get('swp_multi_tenancy.tenant_context')->willReturn($tenantContext);

        $article->setTenantCode('123456')->shouldBeCalled();

        $this->onPublish($event);
    }

    public function it_skips_when_tenant_code_is_already_set(
        ArticleEvent $event,
        ArticleInterface $article,
        TenantContextInterface $tenantContext,
        ContainerInterface $container
    ) {
        $article->getTenantCode()->willReturn('123456');

        $tenant = new Tenant();
        $tenant->setSubdomain('example.com');
        $tenant->setName('Example');
        $tenant->setCode('123456');

        $event->getArticle()->willReturn($article);

        $tenantContext->getTenant()->shouldNotBeCalled();
        $container->get('swp_multi_tenancy.tenant_context')->shouldNotBeCalled();

        $article->setTenantCode('123456')->shouldNotBeCalled();

        $this->onPublish($event);
    }
}
