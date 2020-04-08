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

use JMS\Serializer\Context;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\CoreBundle\Context\ScopeContext;
use SWP\Bundle\CoreBundle\Model\Tenant;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Provider\TenantProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;

final class TenantHandler implements EventSubscriberInterface, SubscribingHandlerInterface
{
    private $settingsManager;

    private $requestStack;

    private $routeRepository;

    private $contentListRepository;

    private $tenantContext;

    private $internalCache = [];

    private $serializer;

    private $tenantProvider;

    private $dispatcher;

    public function __construct(
        SettingsManagerInterface $settingsManager,
        RequestStack $requestStack,
        RouteRepositoryInterface $routeRepository,
        ContentListRepositoryInterface $contentListRepository,
        TenantContextInterface $tenantContext,
        TenantProviderInterface $tenantProvider,
        SerializerInterface $serializer,
        EventDispatcherInterface $dispatcher
    ) {
        $this->settingsManager = $settingsManager;
        $this->requestStack = $requestStack;
        $this->routeRepository = $routeRepository;
        $this->contentListRepository = $contentListRepository;
        $this->tenantContext = $tenantContext;
        $this->serializer = $serializer;
        $this->tenantProvider = $tenantProvider;
        $this->dispatcher = $dispatcher;
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

    public static function getSubscribingMethods(): array
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
        /** @var TenantInterface $tenant */
        $tenant = $event->getObject();
        /** @var JsonSerializationVisitor $visitor */
        $visitor = $event->getVisitor();

        if (isset($this->internalCache[$tenant->getCode()])) {
            $cachedData = $this->internalCache[$tenant->getCode()];
            $visitor->visitProperty(new StaticPropertyMetadata('', 'default_language', null), $cachedData['settings']['defaultLanguage']);
            $visitor->visitProperty(new StaticPropertyMetadata('', 'fbia_enabled', null), $cachedData['settings']['fbiaEnabled']);
            $visitor->visitProperty(new StaticPropertyMetadata('', 'paywall_enabled', null), $cachedData['settings']['paywallEnabled']);
            if (isset($cachedData['routes'])) {
                $visitor->visitProperty(
                    new StaticPropertyMetadata('', 'routes', null, ['api_routes_list']),
                    $cachedData['routes']
                );
            }
            if (isset($cachedData['contentLists'])) {
                $visitor->visitProperty(new StaticPropertyMetadata('', 'content_lists', null), $cachedData['contentLists']);
            }

            return;
        }

        $originalTenant = $this->tenantContext->getTenant();
        if ($originalTenant->getCode() !== $tenant->getCode()) {
            $this->tenantContext->setTenant($tenant);
        }

        $defaultLanguage = $this->settingsManager->get('default_language', ScopeContext::SCOPE_TENANT, $tenant);
        $fbiaEnabled = $this->settingsManager->get('fbia_enabled', ScopeContext::SCOPE_TENANT, $tenant, false);
        $paywallEnabled = $this->settingsManager->get('paywall_enabled', ScopeContext::SCOPE_TENANT, $tenant, false);
        $this->internalCache[$tenant->getCode()]['settings'] = [
            'defaultLanguage' => $defaultLanguage,
            'fbiaEnabled' => $fbiaEnabled,
            'paywallEnabled' => $paywallEnabled,
        ];

        $visitor->visitProperty(new StaticPropertyMetadata('', 'default_language', null), $defaultLanguage);
        $visitor->visitProperty(new StaticPropertyMetadata('', 'fbia_enabled', null), $fbiaEnabled);
        $visitor->visitProperty(new StaticPropertyMetadata('', 'paywall_enabled', null), $paywallEnabled);

        $masterRequest = $this->requestStack->getMasterRequest();
        if (null !== $masterRequest && (null !== $masterRequest->get('withRoutes') || null !== $masterRequest->get('withContentLists'))) {
            $this->dispatcher->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE, new GenericEvent($tenant));
            if (null !== $masterRequest->get('withRoutes')) {
                $routes = $this->routeRepository->getQueryByCriteria(new Criteria(['maxResults' => 9999]), [], 'r')->getQuery()->getResult();
                $routesArray = $this->serializer->toArray($routes, SerializationContext::create()->setGroups(['Default', 'api_routes_list']));
                $this->internalCache[$tenant->getCode()]['routes'] = $routesArray;

                $visitor->visitProperty(new StaticPropertyMetadata('', 'routes', null, ['api_routes_list']), $routesArray);
            }

            if (null !== $masterRequest->get('withContentLists')) {
                $contentLists = $this->contentListRepository->getQueryByCriteria(new Criteria(['maxResults' => 9999]), [], 'cl')->getQuery()->getResult();
                $contentListsArray = $this->serializer->toArray($contentLists, SerializationContext::create()->setGroups(['Default', 'api']));
                $this->internalCache[$tenant->getCode()]['contentLists'] = $contentListsArray;

                $visitor->visitProperty(new StaticPropertyMetadata('', 'content_lists', null), $contentListsArray);
            }
        }

        if ($originalTenant->getCode() !== $tenant->getCode()) {
            $this->tenantContext->setTenant($originalTenant);
        }
    }

    public function serializeToJson(
        JsonSerializationVisitor $visitor,
        string $tenantCode,
        array $type,
        Context $context
    ) {
        $tenant = $this->tenantProvider->findOneByCode($tenantCode);
        if (null === $tenant) {
            return;
        }

        $data = $context->getNavigator()->accept($tenant);
        unset($data['articles_count'], $data['created_at'], $data['enabled'], $data['organization'],$data['theme_name'], $data['updated_at']);

        return $data;
    }
}
