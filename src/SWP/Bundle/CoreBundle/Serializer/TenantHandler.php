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
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use SWP\Bundle\CoreBundle\Context\ScopeContext;
use SWP\Bundle\CoreBundle\Model\Tenant;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\Routing\RouterInterface;

final class TenantHandler implements SubscribingHandlerInterface, EventSubscriberInterface
{
    private $tenantRepository;

    private $router;

    private $settingsManager;

    public function __construct(TenantRepositoryInterface $tenantRepository, RouterInterface $router, SettingsManagerInterface $settingsManager)
    {
        $this->tenantRepository = $tenantRepository;
        $this->router = $router;
        $this->settingsManager = $settingsManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => TenantInterface::class,
                'method' => 'serializeToJson',
            ),
        );
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

    public function onPostSerialize(ObjectEvent $event): void
    {
        $tenant = $event->getObject();
        $event->getVisitor()->setData('fbiaEnabled', $this->settingsManager->get('fbia_enabled', ScopeContext::SCOPE_TENANT, $tenant));
        $event->getVisitor()->setData('paywallEnabled', $this->settingsManager->get('paywall_enabled', ScopeContext::SCOPE_TENANT, $tenant));
    }

    public function serializeToJson(
        JsonSerializationVisitor $visitor,
        string $tenantCode
    ) {
        /** @var TenantInterface $tenant */
        $tenant = $this->tenantRepository->findOneByCode($tenantCode);

        if (null === $tenant) {
            return;
        }

        return [
           'id' => $tenant->getId(),
           'subdomain' => $tenant->getSubdomain(),
           'domainName' => $tenant->getDomainName(),
           'code' => $tenantCode,
           'name' => $tenant->getName(),
           'ampEnabled' => $tenant->isAmpEnabled(),
           'fbiaEnabled' => $this->settingsManager->get('fbia_enabled', ScopeContext::SCOPE_TENANT, $tenant),
           'paywallEnabled' => $this->settingsManager->get('paywall_enabled', ScopeContext::SCOPE_TENANT, $tenant),
           '_links' => [
               'self' => [
                   'href' => $this->router->generate('swp_api_core_get_tenant', ['code' => $tenantCode]),
               ],
           ],
       ];
    }
}
