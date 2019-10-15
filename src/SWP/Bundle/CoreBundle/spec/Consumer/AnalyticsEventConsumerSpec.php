<?php

namespace spec\SWP\Bundle\CoreBundle\Consumer;

use Doctrine\Common\Persistence\ObjectManager;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Prophecy\Argument;
use SWP\Bundle\AnalyticsBundle\Services\ArticleStatisticsServiceInterface;
use SWP\Bundle\CoreBundle\Consumer\AnalyticsEventConsumer;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Model\Tenant;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Resolver\TenantResolver;

class AnalyticsEventConsumerSpec extends ObjectBehavior
{
    public function it_is_initializable(
        ArticleStatisticsServiceInterface $articleStatisticsService,
        TenantResolver $tenantResolver,
        TenantContextInterface $tenantContext,
        ObjectManager $articleStatisticsObjectManager
    ) {
        $this->beConstructedWith($articleStatisticsService, $tenantResolver, $tenantContext, $articleStatisticsObjectManager);
        $this->shouldHaveType(AnalyticsEventConsumer::class);
    }

    public function it_stop_execution_on_invalid_data(
        ArticleStatisticsServiceInterface $articleStatisticsService,
        TenantResolver $tenantResolver,
        TenantContextInterface $tenantContext,
        TenantInterface $tenant,
        AMQPMessage $AMQPMessage,
        ObjectManager $articleStatisticsObjectManager
    ) {
        $tenantResolver->resolve(Argument::type('string'))->willReturn($tenant);
        $articleStatisticsService->addArticleEvent(1, 'pageview', [])->shouldNotBeCalled();

        $tenant = new Tenant();
        $tenant->setDomainName('localhost');
        $tenantContext->getTenant()->willReturn($tenant);

        $this->beConstructedWith($articleStatisticsService, $tenantResolver, $tenantContext, $articleStatisticsObjectManager);

        $AMQPMessage->getBody()->willReturn(serialize([]));
        $this->execute($AMQPMessage)->shouldReturn(ConsumerInterface::MSG_REJECT);
    }
}
