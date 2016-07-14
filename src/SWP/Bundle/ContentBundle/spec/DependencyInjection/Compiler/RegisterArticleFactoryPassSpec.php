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
use Prophecy\Argument;
use SWP\Bundle\ContentBundle\DependencyInjection\Compiler\RegisterArticleFactoryPass;
use SWP\Bundle\ContentBundle\Factory\ArticleFactory;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

/**
 * @mixin RegisterArticleFactoryPass
 */
class RegisterArticleFactoryPassSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(RegisterArticleFactoryPass::class);
    }

    public function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    public function it_creates_a_default_definition_of_article_factory(
        ContainerBuilder $container,
        Definition $routeProviderDefinition,
        Definition $articleProviderDefinition
    ) {
        $container->hasDefinition('swp.factory.article')->willReturn(true);
        $baseDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.article.class'),
            ]
        );

        $container->getParameter('swp.factory.article.class')->willReturn(ArticleFactory::class);
        $container->getParameter('swp_multi_tenancy.persistence.phpcr.content_basepath')->willReturn('content');
        $container->findDefinition('swp.provider.route')->willReturn($routeProviderDefinition);
        $container->findDefinition('swp.provider.article')->willReturn($articleProviderDefinition);

        $articleFactoryDefinition = new Definition(
            ArticleFactory::class,
            [
                $baseDefinition,
                $routeProviderDefinition,
                $articleProviderDefinition,
                'content',
            ]
        );

        $container->setDefinition(
            'swp.factory.article',
            Argument::type(Definition::class)
        )->willReturn($articleFactoryDefinition);

        $this->process($container);
    }
}
