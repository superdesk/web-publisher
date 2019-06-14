<?php

namespace spec\SWP\Bundle\CoreBundle\Consumer;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Persistence\ObjectManager;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Prophecy\Argument;
use SWP\Bundle\AnalyticsBundle\Services\ArticleStatisticsServiceInterface;
use SWP\Bundle\CoreBundle\Consumer\AnalyticsEventConsumer;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Model\Tenant;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Bundle\CoreBundle\Resolver\ArticleResolverInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Resolver\TenantResolver;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class AnalyticsEventConsumerSpec extends ObjectBehavior
{
    public function it_is_initializable(ArticleStatisticsServiceInterface $articleStatisticsService, TenantResolver $tenantResolver, TenantContextInterface $tenantContext, UrlMatcherInterface $matcher, ArticleResolverInterface $articleResolver, ObjectManager $articleStatisticsObjectManager, CacheProvider $cacheProvider)
    {
        $this->beConstructedWith($articleStatisticsService, $tenantResolver, $tenantContext, $matcher, $articleResolver, $articleStatisticsObjectManager, $cacheProvider);
        $this->shouldHaveType(AnalyticsEventConsumer::class);
    }

    public function it_stop_execution_on_invalid_data(ArticleStatisticsServiceInterface $articleStatisticsService, TenantResolver $tenantResolver, TenantContextInterface $tenantContext, TenantInterface $tenant, AMQPMessage $AMQPMessage, UrlMatcherInterface $matcher, ArticleResolverInterface $articleResolver, ObjectManager $articleStatisticsObjectManager, CacheProvider $cacheProvider)
    {
        $tenantResolver->resolve(Argument::type('string'))->willReturn($tenant);
        $articleStatisticsService->addArticleEvent(1, 'pageview', [])->shouldNotBeCalled();

        $tenant = new Tenant();
        $tenant->setDomainName('localhost');
        $tenantContext->getTenant()->willReturn($tenant);

        $this->beConstructedWith($articleStatisticsService, $tenantResolver, $tenantContext, $matcher, $articleResolver, $articleStatisticsObjectManager, $cacheProvider);

        $AMQPMessage->getBody()->willReturn(serialize([]));
        $this->execute($AMQPMessage)->shouldReturn(ConsumerInterface::MSG_REJECT);
    }
}
