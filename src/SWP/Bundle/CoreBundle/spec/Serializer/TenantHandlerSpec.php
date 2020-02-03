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
use JMS\Serializer\SerializerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\CoreBundle\Serializer\TenantHandler;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Provider\TenantProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class TenantHandlerSpec extends ObjectBehavior
{
    public function let(SettingsManagerInterface $settingsManager,
        RequestStack $requestStack,
        RouteRepositoryInterface $routeRepository,
        ContentListRepositoryInterface $contentListRepository,
        TenantContextInterface $tenantContext,
        TenantProviderInterface $tenantProvider,
        SerializerInterface $serializer,
        EventDispatcherInterface $eventDispatcher
    ) {
        $settingsManager->get(Argument::cetera())->willReturn(false);
        $this->beConstructedWith(
            $settingsManager,
            $requestStack,
            $routeRepository,
            $contentListRepository,
            $tenantContext,
            $tenantProvider,
            $serializer,
            $eventDispatcher
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(TenantHandler::class);
    }

    public function it_is_subscribing_handler()
    {
        $this->shouldImplement(SubscribingHandlerInterface::class);
    }
}
