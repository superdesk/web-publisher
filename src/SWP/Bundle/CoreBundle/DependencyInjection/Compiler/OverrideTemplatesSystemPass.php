<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\DependencyInjection\Compiler;

use SWP\Bundle\CoreBundle\Container\RevisionAwareContainerRenderer;
use SWP\Bundle\CoreBundle\Factory\ContainerFactory;
use SWP\Bundle\CoreBundle\Provider\WidgetProvider;
use SWP\Bundle\CoreBundle\Service\RevisionAwareContainerService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class OverrideTemplatesSystemPass extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $containerProvider = $this->getDefinitionIfExists($container, 'swp.provider.widget');
        if (null !== $containerProvider) {
            $containerProvider
                ->setClass(WidgetProvider::class)
                ->addMethodCall('setRevisionContext', [new Reference('swp_revision.context.revision')]);
        }

        $containerRenderer = $this->getDefinitionIfExists($container, 'swp.factory.container_renderer');
        if (null !== $containerRenderer) {
            $containerRenderer
                ->setArguments([
                    RevisionAwareContainerRenderer::class,
                ]);
        }

        $containerFactory = $this->getDefinitionIfExists($container, 'swp.factory.container');
        if (null !== $containerFactory) {
            $containerFactory
                ->setClass(ContainerFactory::class)
                ->setArguments([
                    $container->getParameter('swp.model.container.class'),
                    new Reference('swp_multi_tenancy.random_string_generator'),
                ]);
        }

        $containerService = $this->getDefinitionIfExists($container, 'swp_template_engine.container.service');
        if (null !== $containerService) {
            $containerService
                ->setClass(RevisionAwareContainerService::class);
        }
    }
}
