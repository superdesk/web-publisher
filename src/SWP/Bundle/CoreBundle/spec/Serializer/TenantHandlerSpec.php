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

namespace spec\SWP\Bundle\CoreBundle\Serializer;

use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Bundle\CoreBundle\Serializer\TenantHandler;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\Routing\RouterInterface;

final class TenantHandlerSpec extends ObjectBehavior
{
    public function let(TenantRepositoryInterface $tenantRepository, RouterInterface $router, SettingsManagerInterface $settingsManager)
    {
        $settingsManager->get(Argument::cetera())->willReturn(false);
        $this->beConstructedWith($tenantRepository, $router, $settingsManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(TenantHandler::class);
    }

    public function it_is_subscribing_handler()
    {
        $this->shouldImplement(SubscribingHandlerInterface::class);
    }

    public function it_serializes_to_json(
        TenantRepositoryInterface $tenantRepository,
        TenantInterface $tenant,
        RouterInterface $router
    ) {
        $tenant->getId()->willReturn(1);
        $tenant->getName()->willReturn('Default');
        $tenant->getSubdomain()->willReturn('subdomain');
        $tenant->getDomainName()->willReturn('domain.com');
        $tenant->getCode()->willReturn('123abc');
        $tenant->isAmpEnabled()->willReturn(true);

        $tenantRepository->findOneByCode('123abc')->willReturn($tenant);

        $router->generate('swp_api_core_get_tenant', ['code' => '123abc'])->willReturn('url');

        $this->serializeToJson(new JsonSerializationVisitor(), '123abc')->shouldReturn([
            'id' => 1,
            'subdomain' => 'subdomain',
            'domain_name' => 'domain.com',
            'code' => '123abc',
            'name' => 'Default',
            'amp_enabled' => true,
            'fbia_enabled' => false,
            'paywall_enabled' => false,
            '_links' => [
                'self' => [
                    'href' => 'url',
                ],
            ],
        ]);
    }
}
