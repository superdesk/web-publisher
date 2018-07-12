<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Provider;

use Doctrine\Common\Cache\Cache;
use SWP\Component\Paywall\Model\SubscriberInterface;

final class CachedSubscriptionsProvider implements SubscriptionsProviderInterface
{
    public const CACHE_KEY_PREFIX = 'subscriptions_provider_';

    /**
     * @var SubscriptionsProviderInterface
     */
    private $decoratedProvider;

    /**
     * @var Cache
     */
    private $cacheProvider;

    public function __construct(SubscriptionsProviderInterface $decoratedProvider, Cache $cacheProvider)
    {
        $this->decoratedProvider = $decoratedProvider;
        $this->cacheProvider = $cacheProvider;
    }

    public function getSubscriptions(SubscriberInterface $subscriber, array $filters = []): array
    {
        $cacheKey = $this->generateCacheKey($subscriber);

        if ($this->cacheProvider->contains($cacheKey)) {
            $subscriptions = $this->cacheProvider->fetch($cacheKey);
        } else {
            $subscriptions = $this->decoratedProvider->getSubscriptions($subscriber, $filters);

            $this->cacheProvider->save($cacheKey, $subscriptions, 86400);
        }

        return $subscriptions;
    }

    private function generateCacheKey(SubscriberInterface $subscriber): string
    {
        return self::CACHE_KEY_PREFIX.$subscriber->getSubscriberId();
    }
}
