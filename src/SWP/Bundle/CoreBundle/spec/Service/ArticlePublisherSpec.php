<?php

declare(strict_types=1);

namespace spec\SWP\Bundle\CoreBundle\Service;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Factory\ArticleFactoryInterface;
use SWP\Bundle\CoreBundle\Service\ArticlePublisher;
use SWP\Bundle\CoreBundle\Service\ArticlePublisherInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ArticlePublisherSpec extends ObjectBehavior
{
    public function let(
        ArticleRepositoryInterface $articleRepository,
        EventDispatcherInterface $eventDispatcher,
        ArticleFactoryInterface $articleFactory,
        TenantContextInterface $tenantContext
    ) {
        $this->beConstructedWith($articleRepository, $eventDispatcher, $articleFactory, $tenantContext);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ArticlePublisher::class);
    }

    public function it_implements_interface()
    {
        $this->shouldImplement(ArticlePublisherInterface::class);
    }
}
