<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\ContentBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\ContentBundle\DependencyInjection\Compiler\RegisterORMArticleFactoryPass;
use SWP\Bundle\ContentBundle\Factory\ORM\ArticleFactory;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

/**
 * @mixin RegisterORMArticleFactoryPass
 */
class RegisterORMArticleFactoryPassSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(RegisterORMArticleFactoryPass::class);
    }

    public function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    public function it_creates_a_default_definition_of_article_factory(
        ContainerBuilder $container,
        Definition $articleHydrator
    ) {
        $container->hasDefinition('swp.factory.article')->willReturn(true);
        $baseDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.article.class'),
            ]
        );

        $container->getParameter('swp.factory.article.class')->willReturn(ArticleFactory::class);
        $container->hasParameter('swp_content.backend_type_orm')->willReturn(true);
        $container->findDefinition('swp.hydrator.article')->willReturn($articleHydrator);

        $articleFactoryDefinition = new Definition(
            ArticleFactory::class,
            [
                $baseDefinition,
                $articleHydrator,
            ]
        );

        $container->setDefinition(
            'swp.factory.article',
            Argument::type(Definition::class)
        )->willReturn($articleFactoryDefinition);

        $this->process($container);
    }
}
