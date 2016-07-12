<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\ContentBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\DependencyInjection\Compiler\RegisterRouteFactoryPass;
use SWP\Bundle\ContentBundle\Factory\RouteFactory;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

/**
 * @mixin RegisterRouteFactoryPass
 */
class RegisterRouteFactoryPassSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(RegisterRouteFactoryPass::class);
    }

    public function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    public function it_creates_a_default_definition_of_route_factory(ContainerBuilder $container)
    {
        $container->hasDefinition('swp.factory.route')->willReturn(true);
        $baseDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.route.class'),
            ]
        );

        $container->getParameter('swp.factory.route.class')->willReturn(RouteFactory::class);
        $routeFactoryDefinition = new Definition(
            RouteFactory::class,
            [
                $baseDefinition,
            ]
        );

        $container->setDefinition('swp.factory.route', $routeFactoryDefinition)->shouldBeCalled();

        $this->process($container);
    }

    public function it_does_not_create_default_definition_of_route_factory_if_route_factory_is_not_set(
        ContainerBuilder $container
    ) {
        $container->hasDefinition('swp.factory.route')->willReturn(false);
        $baseChannelFactoryDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.route.class'),
            ]
        );

        $container->getParameter('swp.factory.route.class')->shouldNotBeCalled();
        $routeFactoryDefinition = new Definition(
            RouteFactory::class,
            [
                $baseChannelFactoryDefinition,
            ]
        );
        $container->setDefinition('swp.factory.route', $routeFactoryDefinition)->shouldNotBeCalled();

        $this->process($container);
    }
}
