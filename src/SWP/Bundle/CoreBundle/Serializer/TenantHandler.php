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

namespace SWP\Bundle\CoreBundle\Serializer;

use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\Context;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\CoreBundle\Context\ScopeContext;
use SWP\Bundle\CoreBundle\Model\Tenant;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class TenantHandler implements EventSubscriberInterface, SubscribingHandlerInterface
{
    private $settingsManager;

    private $requestStack;

    private $routeRepository;

    private $contentListRepository;

    private $tenantContext;

    private $tenantRepository;

    private $cachedTenants = [];

    public function __construct(
        SettingsManagerInterface $settingsManager,
        RequestStack $requestStack,
        RouteRepositoryInterface $routeRepository,
        ContentListRepositoryInterface $contentListRepository,
        TenantContextInterface $tenantContext,
        TenantRepositoryInterface $tenantRepository
    ) {
        $this->settingsManager = $settingsManager;
        $this->requestStack = $requestStack;
        $this->routeRepository = $routeRepository;
        $this->contentListRepository = $contentListRepository;
        $this->tenantContext = $tenantContext;
        $this->tenantRepository = $tenantRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            [
                'event' => Events::POST_SERIALIZE,
                'class' => Tenant::class,
                'method' => 'onPostSerialize',
            ],
        ];
    }

    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => TenantInterface::class,
                'method' => 'serializeToJson',
            ],
        ];
    }

    public function onPostSerialize(ObjectEvent $event): void
    {
        $tenant = $event->getObject();
        $originalTenant = $this->tenantContext->getTenant();
        $this->tenantContext->setTenant($tenant);

        /** @var JsonSerializationVisitor $visitor */
        $visitor = $event->getVisitor();
        $visitor->visitProperty(new StaticPropertyMetadata('', 'fbia_enabled', null), $this->settingsManager->get('fbia_enabled', ScopeContext::SCOPE_TENANT, $tenant, false));
        $visitor->visitProperty(new StaticPropertyMetadata('', 'paywall_enabled', null), $this->settingsManager->get('paywall_enabled', ScopeContext::SCOPE_TENANT, $tenant, false));

        $masterRequest = $this->requestStack->getMasterRequest();
        if (null !== $masterRequest && (null !== $masterRequest->get('withRoutes') || null !== $masterRequest->get('withContentLists'))) {
            if (null !== $masterRequest->get('withRoutes')) {
                $routes = $this->routeRepository->getQueryByCriteria(new Criteria(), [], 'r')->getQuery()->getResult();
                $visitor->visitProperty(new StaticPropertyMetadata('', 'routes', null), $routes);
            }

            if (null !== $masterRequest->get('withContentLists')) {
                $contentLists = $this->contentListRepository->getQueryByCriteria(new Criteria(), [], 'cl')->getQuery()->getResult();
                $visitor->visitProperty(new StaticPropertyMetadata('', 'content_lists', null), $contentLists);
            }
        }
        $this->tenantContext->setTenant($originalTenant);
    }

    public function serializeToJson(
        JsonSerializationVisitor $visitor,
        string $tenantCode,
        array $type,
        Context $context
    ) {
        if (array_key_exists($tenantCode, $this->cachedTenants)) {
            $tenant = $this->cachedTenants[$tenantCode];
        } else {
            /** @var TenantInterface $tenant */
            $tenant = $this->tenantRepository->findOneByCode($tenantCode);
            $this->cachedTenants[$tenantCode] = $tenant;
        }

        if (null === $tenant) {
            return;
        }

        $data = $context->getNavigator()->accept($tenant);
        unset($data['articles_count'], $data['created_at'], $data['enabled'], $data['organization'],$data['theme_name'], $data['updated_at']);

        return $data;
    }
}
