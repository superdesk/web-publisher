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

namespace SWP\Bundle\CoreBundle\EventSubscriber;

use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ArticlePublishSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * ArticlePublishSubscriber constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ArticleEvents::POST_PUBLISH => 'onPublish',
        ];
    }

    /**
     * @param ArticleEvent $event
     */
    public function onPublish(ArticleEvent $event)
    {
        /** @var ArticleInterface $article */
        $article = $event->getArticle();

        if (null === $article->getTenantCode()) {
            $tenantContext = $this->container->get('swp_multi_tenancy.tenant_context');
            $article->setTenantCode($tenantContext->getTenant()->getCode());
        }
    }
}
