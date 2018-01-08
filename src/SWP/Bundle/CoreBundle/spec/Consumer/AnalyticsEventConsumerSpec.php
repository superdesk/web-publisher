<?php

namespace spec\SWP\Bundle\CoreBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Prophecy\Argument;
use SWP\Bundle\AnalyticsBundle\Services\ArticleStatisticsServiceInterface;
use SWP\Bundle\CoreBundle\Consumer\AnalyticsEventConsumer;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Resolver\TenantResolver;
use Symfony\Component\HttpFoundation\Request;

class AnalyticsEventConsumerSpec extends ObjectBehavior
{
    public function it_is_initializable(ArticleStatisticsServiceInterface $articleStatisticsService, TenantResolver $tenantResolver, TenantContextInterface $tenantContext, TenantInterface $tenant)
    {
        $this->beConstructedWith($articleStatisticsService, $tenantResolver, $tenantContext);
        $this->shouldHaveType(AnalyticsEventConsumer::class);
    }

    public function it_executes(ArticleStatisticsServiceInterface $articleStatisticsService, TenantResolver $tenantResolver, TenantContextInterface $tenantContext, TenantInterface $tenant, AMQPMessage $AMQPMessage)
    {
        $tenantResolver->resolve(Argument::type('string'))->willReturn($tenant);
        $articleStatisticsService->addArticleEvent(1, 'pageview')->shouldBeCalled();
        $this->beConstructedWith($articleStatisticsService, $tenantResolver, $tenantContext);

        $request = new Request();
        $request->query->set('articleId', 1);
        $AMQPMessage->getBody()->willReturn(serialize($request));
        $this->execute($AMQPMessage)->shouldReturn(ConsumerInterface::MSG_ACK);
    }

    public function it_stop_execution_on_invalid_data(ArticleStatisticsServiceInterface $articleStatisticsService, TenantResolver $tenantResolver, TenantContextInterface $tenantContext, TenantInterface $tenant, AMQPMessage $AMQPMessage)
    {
        $tenantResolver->resolve(Argument::type('string'))->willReturn($tenant);
        $articleStatisticsService->addArticleEvent(1, 'pageview')->shouldNotBeCalled();
        $this->beConstructedWith($articleStatisticsService, $tenantResolver, $tenantContext);

        $AMQPMessage->getBody()->willReturn(serialize([]));
        $this->execute($AMQPMessage)->shouldReturn(ConsumerInterface::MSG_REJECT);
    }
}
