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

namespace SWP\Bundle\CoreBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use SWP\Bundle\AnalyticsBundle\Model\ArticleEventsInterface;
use SWP\Bundle\AnalyticsBundle\Services\ArticleStatisticsServiceInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Resolver\TenantResolver;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AnalyticsEventConsumer.
 */
class AnalyticsEventConsumer implements ConsumerInterface
{
    /**
     * @var ArticleStatisticsServiceInterface
     */
    protected $articleStatisticsService;

    /**
     * @var TenantResolver
     */
    protected $tenantResolver;

    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    /**
     * AnalyticsEventConsumer constructor.
     *
     * @param ArticleStatisticsServiceInterface $articleStatisticsService
     * @param TenantResolver                    $tenantResolver
     * @param TenantContextInterface            $tenantContext
     */
    public function __construct(ArticleStatisticsServiceInterface $articleStatisticsService, TenantResolver $tenantResolver, TenantContextInterface $tenantContext)
    {
        $this->articleStatisticsService = $articleStatisticsService;
        $this->tenantResolver = $tenantResolver;
        $this->tenantContext = $tenantContext;
    }

    /**
     * @param AMQPMessage $message
     *
     * @return bool|mixed
     */
    public function execute(AMQPMessage $message)
    {
        /** @var Request $request */
        $request = unserialize($message->getBody());
        if (!$request instanceof Request) {
            return ConsumerInterface::MSG_REJECT;
        }

        $this->setTenant($request);
        $articleId = $request->query->get('articleId', null);
        $action = $request->query->get('action', ArticleEventsInterface::ACTION_PAGEVIEW);

        if (null !== $articleId) {
            $this->articleStatisticsService->addArticleEvent((int) $articleId, $action);
        }

        return ConsumerInterface::MSG_ACK;
    }

    /**
     * @param Request $request
     */
    private function setTenant(Request $request)
    {
        $this->tenantContext->setTenant($this->tenantResolver->resolve($request->getHost()));
    }
}
