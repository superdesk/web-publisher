<?php

/*
 * This file is part of the Superdesk Web Publisher MultiTenancyBundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler;

use SWP\Component\MultiTenancy\Factory\OrganizationFactory;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

class RegisterOrganizationFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('swp.factory.organization')) {
            return;
        }

        $factoryDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.organization.class'),
            ]
        );

        $organizationFactoryDefinition = new Definition(
            OrganizationFactory::class,
            [
                $factoryDefinition,
                new Reference('swp_multi_tenancy.random_string_generator'),
            ]
        );

        $organizationFactoryDefinition = new Definition(
            $container->getParameter('swp.factory.organization.class'),
            [
                $organizationFactoryDefinition,
                new Reference('swp.object_manager.organization'),
                new Parameter('swp_multi_tenancy.persistence.phpcr.basepath'),
            ]
        );

        $container->setDefinition('swp.factory.organization', $organizationFactoryDefinition);
    }
}
