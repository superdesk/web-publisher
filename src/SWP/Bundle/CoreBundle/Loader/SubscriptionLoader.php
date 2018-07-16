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

namespace SWP\Bundle\CoreBundle\Loader;

use Doctrine\Common\Collections\ArrayCollection;
use SWP\Bundle\ContentBundle\Loader\PaginatedLoader;
use SWP\Bundle\CoreBundle\Provider\SubscriptionsProviderInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Paywall\Model\SubscriberInterface;
use SWP\Component\TemplatesSystem\Gimme\Factory\MetaFactoryInterface;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;
use SWP\Component\TemplatesSystem\Gimme\Meta\MetaCollection;

final class SubscriptionLoader extends PaginatedLoader implements LoaderInterface
{
    public const SUPPORTED_TYPES = ['subscriptions'];

    /**
     * @var MetaFactoryInterface
     */
    private $metaFactory;

    /**
     * @var SubscriptionsProviderInterface
     */
    private $subscriptionsProvider;

    public function __construct(
        MetaFactoryInterface $metaFactory,
        SubscriptionsProviderInterface $subscriptionsProvider
    ) {
        $this->metaFactory = $metaFactory;
        $this->subscriptionsProvider = $subscriptionsProvider;
    }

    public function load($type, $parameters = [], $withoutParameters = [], $responseType = LoaderInterface::SINGLE)
    {
        if (!array_key_exists('user', $parameters)) {
            return false;
        }

        if (!($user = $parameters['user']) instanceof SubscriberInterface) {
            return false;
        }

        $filters = [
            'routeId' => $parameters['routeId'] ?? null,
            'articleId' => $parameters['articleId'] ?? null,
        ];

        $this->applyPaginationToCriteria(new Criteria(), $parameters);
        $subscriptions = $this->subscriptionsProvider->getSubscriptions($user, $filters);

        $subscriptions = new ArrayCollection($subscriptions);

        if (0 === $subscriptions->count()) {
            return false;
        }

        $metaCollection = new MetaCollection();
        $metaCollection->setTotalItemsCount($subscriptions->count());

        foreach ($subscriptions as $subscription) {
            $meta = $this->metaFactory->create($subscription);
            if (null !== $meta) {
                $metaCollection->add($meta);
            }
        }
        unset($subscriptions, $criteria);

        return $metaCollection;
    }

    public function isSupported(string $type): bool
    {
        return \in_array($type, self::SUPPORTED_TYPES, true);
    }
}
