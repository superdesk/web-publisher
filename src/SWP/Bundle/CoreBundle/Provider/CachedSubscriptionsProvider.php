<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Provider;

use Doctrine\Common\Cache\Cache;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\Paywall\Model\SubscriberInterface;

final class CachedSubscriptionsProvider implements SubscriptionsProviderInterface
{
    public const CACHE_KEY_PREFIX = 'subscriptions_provider_';

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
        $cacheKey = $this->generateCacheKey($subscriber);

        if ($this->cacheProvider->contains($cacheKey)) {
            $subscriptions = $this->cacheProvider->fetch($cacheKey);
        } else {
            $subscriptions = $this->decoratedProvider->getSubscriptions($subscriber, $filters);

            $this->cacheProvider->save($cacheKey, $subscriptions, $this->cacheLifeTime);
        }

        return $subscriptions;
    }

    private function generateCacheKey(SubscriberInterface $subscriber): string
    {
        return self::CACHE_KEY_PREFIX.$this->tenantContext->getTenant()->getCode().$subscriber->getSubscriberId();
    }
}
