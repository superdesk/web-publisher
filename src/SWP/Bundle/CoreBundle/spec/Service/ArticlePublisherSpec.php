<?php

declare(strict_types=1);

namespace spec\SWP\Bundle\CoreBundle\Service;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Factory\ArticleFactoryInterface;
use SWP\Bundle\ContentListBundle\Services\ContentListServiceInterface;
use SWP\Bundle\CoreBundle\Repository\ContentListItemRepository;
use SWP\Bundle\CoreBundle\Service\ArticlePublisher;
use SWP\Bundle\CoreBundle\Service\ArticlePublisherInterface;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ArticlePublisherSpec extends ObjectBehavior
{
    public function let(
        ArticleRepositoryInterface $articleRepository,
        EventDispatcherInterface $eventDispatcher,
        ArticleFactoryInterface $articleFactory,
        FactoryInterface $factory,
        TenantContextInterface $tenantContext,
        ContentListRepositoryInterface $contentListRepository,
        ContentListItemRepository $contentListItemRepository,
        ContentListServiceInterface $contentListService
    ) {
        $this->beConstructedWith(
            $articleRepository,
            $eventDispatcher,
            $articleFactory,
            $factory,
            $tenantContext,
            $contentListRepository,
            $contentListItemRepository,
            $contentListService
        );
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
