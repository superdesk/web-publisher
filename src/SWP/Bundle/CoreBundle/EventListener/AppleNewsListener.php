<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class AppleNewsListener
{
    private $messageBus;

    private $tenantContext;

    public function __construct(
        MessageBusInterface $messageBus,
        TenantContextInterface $tenantContext
    ) {
        $this->messageBus = $messageBus;
        $this->tenantContext = $tenantContext;
    }

    public function publish(ArticleEvent $articleEvent): void
    {
        $article = $articleEvent->getArticle();

        $currentTenant = $this->tenantContext->getTenant();

        $this->messageBus->dispatch(new PublishToAppleNews($article->getId(), $currentTenant->getId()));
    }
}
