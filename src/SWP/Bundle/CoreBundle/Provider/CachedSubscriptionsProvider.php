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

namespace SWP\Bundle\CoreBundle\Provider;

use Doctrine\Common\Cache\Cache;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\Paywall\Model\SubscriberInterface;
use SWP\Component\Paywall\Model\SubscriptionInterface;

final class CachedSubscriptionsProvider implements SubscriptionsProviderInterface
{
    public const CACHE_KEY_PREFIX = 'subscriptions_provider_';

    public const CACHE_KEY_VALID = 'subscription_provider_';

    /**
     * @var int
     */
    private $cacheLifeTime;

    /**
     * @var SubscriptionsProviderInterface
     */
    private $decoratedProvider;

    /**
     * @var Cache
     */
    private $cacheProvider;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    public function __construct(
        int $cacheLifeTime,
        SubscriptionsProviderInterface $decoratedProvider,
        Cache $cacheProvider,
        TenantContextInterface $tenantContext
    ) {
        $this->cacheLifeTime = $cacheLifeTime;
        $this->decoratedProvider = $decoratedProvider;
        $this->cacheProvider = $cacheProvider;
        $this->tenantContext = $tenantContext;
    }

    public function getSubscriptions(SubscriberInterface $subscriber, array $filters = []): array
    {
        $cacheKey = urlencode($this->generateCacheKey($subscriber).implode('_', $filters));

        if ($this->cacheProvider->contains($cacheKey)) {
            $subscriptions = $this->cacheProvider->fetch($cacheKey);
        } else {
            $subscriptions = $this->decoratedProvider->getSubscriptions($subscriber, $filters);

            $this->cacheProvider->save($cacheKey, $subscriptions, $this->cacheLifeTime);
        }

        return $subscriptions;
    }

    public function getSubscription(SubscriberInterface $subscriber, array $filters = []): ?SubscriptionInterface
    {
        $cacheKey = urlencode($this->generateCacheKey($subscriber, self::CACHE_KEY_VALID).implode('_', $filters));

        if ($this->cacheProvider->contains($cacheKey)) {
            $subscription = $this->cacheProvider->fetch($cacheKey);
        } else {
            $subscription = $this->decoratedProvider->getSubscription($subscriber, $filters);

            $this->cacheProvider->save($cacheKey, $subscription, $this->cacheLifeTime);
        }

        return $subscription;
    }

    private function generateCacheKey(SubscriberInterface $subscriber, string $prefix = self::CACHE_KEY_PREFIX): string
    {
        return $prefix.$this->tenantContext->getTenant()->getCode().$subscriber->getSubscriberId();
    }
}
