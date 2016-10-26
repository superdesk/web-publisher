<?php

/*
 * This file is part of the Superdesk Web Publisher Content List Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\ContentListBundle\DependencyInjection\Compiler;

use SWP\Bundle\ContentListBundle\DependencyInjection\Compiler\RegisterContentListFactoryPass;
use PhpSpec\ObjectBehavior;
use SWP\Component\ContentList\Factory\ContentListFactory;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

/**
 * @mixin RegisterContentListFactoryPass
 */
final class RegisterContentListFactoryPassSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(RegisterContentListFactoryPass::class);
    }

    public function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    public function it_creates_a_default_definition_of_content_list_factory(ContainerBuilder $container)
    {
        $container->hasDefinition('swp.factory.content_list')->willReturn(true);
        $baseDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.content_list.class'),
            ]
        );

        $container->getParameter('swp.factory.content_list.class')->willReturn(ContentListFactory::class);
        $factoryDefinition = new Definition(
            ContentListFactory::class,
            [
                $baseDefinition,
            ]
        );

        $container->setDefinition('swp.factory.content_list', $factoryDefinition)->shouldBeCalled();

        $this->process($container);
    }

    public function it_does_not_create_default_definition_of_content_list(
        ContainerBuilder $container
    ) {
        $container->hasDefinition('swp.factory.content_list')->willReturn(false);
        $factoryDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.content_list.class'),
            ]
        );

        $container->getParameter('swp.factory.content_list.class')->shouldNotBeCalled();
        $factoryDefinition = new Definition(
            ContentListFactory::class,
            [
                $factoryDefinition,
            ]
        );
        $container->setDefinition('swp.factory.content_list', $factoryDefinition)->shouldNotBeCalled();

        $this->process($container);
    }
}
