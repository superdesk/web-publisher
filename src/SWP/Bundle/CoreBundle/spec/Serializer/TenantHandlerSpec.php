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
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Bundle\CoreBundle\Serializer\TenantHandler;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\Routing\RouterInterface;

final class TenantHandlerSpec extends ObjectBehavior
{
    public function let(TenantRepositoryInterface $tenantRepository, RouterInterface $router)
    {
        $this->beConstructedWith($tenantRepository, $router);
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
        JsonSerializationVisitor $visitor,
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

        $this->serializeToJson($visitor, '123abc')->shouldReturn([
            'id' => 1,
            'subdomain' => 'subdomain',
            'domainName' => 'domain.com',
            'name' => 'Default',
            'ampEnabled' => true,
            '_links' => [
                'self' => [
                    'href' => 'url',
                ],
            ],
        ]);
    }
}
