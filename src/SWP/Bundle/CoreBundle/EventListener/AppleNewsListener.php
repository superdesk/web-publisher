<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\CoreBundle\MessageHandler\Message\PublishToAppleNews;
use SWP\Bundle\CoreBundle\MessageHandler\Message\UnpublishFromAppleNews;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
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
        /** @var ArticleInterface $article */
        $article = $articleEvent->getArticle();

        /** @var TenantInterface $currentTenant */
        $currentTenant = $this->tenantContext->getTenant();

        if (false === $article->isPublishedToAppleNews() && (null === $currentTenant->getAppleNewsConfig())) {
            return;
        }

        $this->messageBus->dispatch(new PublishToAppleNews($article->getId(), $currentTenant->getId()));
    }

    public function unpublish(ArticleEvent $articleEvent): void
    {
        $article = $articleEvent->getArticle();

        $currentTenant = $this->tenantContext->getTenant();
        if (false === $article->isPublishedToAppleNews() && (null === $currentTenant->getAppleNewsConfig())) {
            return;
        }

        $this->messageBus->dispatch(new UnpublishFromAppleNews($article->getId(), $currentTenant->getId()));
    }
}
