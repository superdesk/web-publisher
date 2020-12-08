<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\DependencyInjection\Compiler;

use SWP\Bundle\UserBundle\Mailer\Mailer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class OverrideFosMailerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $serviceId = 'swp_user.mailer.default';
        $multitenancyContextServiceId = 'swp_multi_tenancy.tenant_context';
        if (!$container->hasDefinition($serviceId) || !$container->hasDefinition($multitenancyContextServiceId)) {
            return;
        }

        $mailerService = $container->getDefinition($serviceId);
        $mailerService
            ->setClass(Mailer::class)
            ->addArgument(new Reference('swp_settings.manager.settings'))
            ->addArgument(new Reference($multitenancyContextServiceId))
        ;
    }
}
