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
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\OutputChannel\Provider\AdapterProviderChain;

final class SendArticleToOutputChannelListener
{
    /**
     * @var AdapterProviderChain
     */
    private $adapterProviderChain;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * SendArticleToOutputChannelListener constructor.
     *
     * @param AdapterProviderChain   $adapterProviderChain
     * @param TenantContextInterface $tenantContext
     */
    public function __construct(
        AdapterProviderChain $adapterProviderChain,
        TenantContextInterface $tenantContext
    ) {
        $this->adapterProviderChain = $adapterProviderChain;
        $this->tenantContext = $tenantContext;
    }

    /**
     * @param ArticleEvent $event
     */
    public function send(ArticleEvent $event)
    {
        /** @var ArticleInterface $article */
        $article = $event->getArticle();
        $tenant = $this->tenantContext->getTenant();

        if (null === $outputChannel = $tenant->getOutputChannel()) {
            return;
        }

        $adapter = $this->adapterProviderChain->get($outputChannel);

        $adapter->send($article);
    }
}
