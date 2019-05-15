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

use SWP\Bundle\CoreBundle\Manager\AuthorMediaManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class OverrideMediaManagerPass extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $mediaManager = $this->getDefinitionIfExists($container, 'swp_content_bundle.manager.media');
        $authorMediaManager = new Definition(AuthorMediaManager::class);
        $authorMediaManager
            ->setArguments($mediaManager->getArguments())
            ->setArgument(6, $this->getDefinitionIfExists($container, 'swp.resolver.asset_location'))
            ->setPublic(true)
        ;
        $container->setDefinition('swp_core_bundle.manager.author_media', $authorMediaManager);
    }
}
