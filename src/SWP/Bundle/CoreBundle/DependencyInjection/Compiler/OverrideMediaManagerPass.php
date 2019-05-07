<?php

declare(strict_types=1);

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

use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\CoreBundle\Manager\AuthorMediaManager;
use SWP\Bundle\CoreBundle\Manager\MediaManager;
use SWP\Bundle\CoreBundle\Manager\SeoMediaManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class OverrideMediaManagerPass extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $mediaManager = $this->getDefinitionIfExists($container, 'swp_content_bundle.manager.media');
        $mediaManager
            ->setClass(MediaManager::class)
            ->setPublic(true)
            ->addMethodCall('setTenantContext', [new Reference('swp_multi_tenancy.tenant_context')])
        ;

        $authorMediaManager = new Definition(AuthorMediaManager::class);
        $authorMediaManager
            ->setArguments($mediaManager->getArguments())
            ->setPublic(true)
            ->addMethodCall('setTenantContext', [new Reference('swp_multi_tenancy.tenant_context')])
        ;
        $container->setDefinition('swp_core_bundle.manager.author_media', $authorMediaManager);

        $seoMediaManager = new Definition(SeoMediaManager::class);
        $seoMediaManager
            ->setArguments($mediaManager->getArguments())
            ->setPublic(true)
            ->addMethodCall('setTenantContext', [new Reference('swp_multi_tenancy.tenant_context')])
        ;

        $container->setDefinition('swp_core_bundle.manager.seo_media', $seoMediaManager);

        $container->registerAliasForArgument(
            'swp_core_bundle.manager.author_media',
            MediaManagerInterface::class,
            'author media manager'
        );

        $container->registerAliasForArgument(
            'swp_core_bundle.manager.seo_media',
            MediaManagerInterface::class,
            'seo media manager'
        );
    }
}
