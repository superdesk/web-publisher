<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\CoreBundle\Adapter\AdapterInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;

final class SendArticleToOutputChannelListener
{
    /**
     * @var AdapterInterface
     */
    private $compositeAdapter;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * SendArticleToOutputChannelListener constructor.
     *
     * @param AdapterInterface       $compositeAdapter
     * @param TenantContextInterface $tenantContext
     */
    public function __construct(AdapterInterface $compositeAdapter, TenantContextInterface $tenantContext)
    {
        $this->compositeAdapter = $compositeAdapter;
        $this->tenantContext = $tenantContext;
    }

    /**
     * @param ArticleEvent $event
     */
    public function create(ArticleEvent $event): void
    {
        if (null !== $outputChannel = $this->tenantContext->getTenant()->getOutputChannel()) {
            $this->compositeAdapter->create($outputChannel, $event->getArticle());
        }
    }

    /**
     * @param ArticleEvent $event
     */
    public function update(ArticleEvent $event): void
    {
        if (null !== $outputChannel = $this->tenantContext->getTenant()->getOutputChannel()) {
            $this->compositeAdapter->update($outputChannel, $event->getArticle());
        }
    }

    /**
     * @param ArticleEvent $event
     */
    public function publish(ArticleEvent $event): void
    {
        if (null !== $outputChannel = $this->tenantContext->getTenant()->getOutputChannel()) {
            $this->compositeAdapter->publish($outputChannel, $event->getArticle());
        }
    }

    /**
     * @param ArticleEvent $event
     */
    public function unpublish(ArticleEvent $event): void
    {
        if (null !== $outputChannel = $this->tenantContext->getTenant()->getOutputChannel()) {
            $this->compositeAdapter->unpublish($outputChannel, $event->getArticle());
        }
    }
}
