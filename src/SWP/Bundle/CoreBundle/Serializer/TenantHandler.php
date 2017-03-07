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

use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\Routing\RouterInterface;

final class TenantHandler implements SubscribingHandlerInterface
{
    /**
     * @var TenantRepositoryInterface
     */
    private $tenantRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * TenantHandler constructor.
     *
     * @param TenantRepositoryInterface $tenantRepository
     * @param RouterInterface           $router
     */
    public function __construct(TenantRepositoryInterface $tenantRepository, RouterInterface $router)
    {
        $this->tenantRepository = $tenantRepository;
        $this->router = $router;
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
           'name' => $tenant->getName(),
           'ampEnabled' => $tenant->isAmpEnabled(),
            '_links' => [
                'self' => [
                    'href' => $this->router->generate('swp_api_core_get_tenant', ['code' => $tenantCode]),
                ],
            ],
       ];
    }
}
