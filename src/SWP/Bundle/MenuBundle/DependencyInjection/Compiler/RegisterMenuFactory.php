<?php

namespace SWP\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

class RegisterMenuFactory implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('swp.factory.menu')) {
            return;
        }

        $menuFactoryDefinition = new Definition(
            $container->getParameter('swp.factory.menu.class'),
            [
                new Parameter('swp.model.menu.class'),
            ]
        );

        $container->setDefinition('swp.factory.menu', $menuFactoryDefinition);
    }
}
