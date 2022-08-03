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

use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\Paywall\Model\SubscriberInterface;
use SWP\Component\Paywall\Model\SubscriptionInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

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
     * @var CacheInterface
     */
    private $cacheProvider;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    public function __construct(
        int $cacheLifeTime,
        SubscriptionsProviderInterface $decoratedProvider,
        CacheInterface $cacheProvider,
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

        return $this->cacheProvider->get($cacheKey, function (ItemInterface $item) use ($subscriber, $filters) {
          $item->expiresAfter($this->cacheLifeTime);
          return $this->decoratedProvider->getSubscriptions($subscriber, $filters);
        });
    }

    public function getSubscription(SubscriberInterface $subscriber, array $filters = []): ?SubscriptionInterface
    {
        $cacheKey = urlencode($this->generateCacheKey($subscriber, self::CACHE_KEY_VALID).implode('_', $filters));
        return $this->cacheProvider->get($cacheKey, function (ItemInterface $item) use ($subscriber, $filters) {
          $item->expiresAfter($this->cacheLifeTime);
          return $this->decoratedProvider->getSubscription($subscriber, $filters);
        });
    }

    private function generateCacheKey(SubscriberInterface $subscriber, string $prefix = self::CACHE_KEY_PREFIX): string
    {
        return $prefix.$this->tenantContext->getTenant()->getCode().$subscriber->getSubscriberId();
    }
}
