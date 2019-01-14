<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\DependencyInjection\Compiler;

use SWP\Bundle\CoreBundle\EventListener\ArticlePublishListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class OverrideArticlePublishListenerPass extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $this->overrideDefinitionClassIfExists(
            $container,
            'swp_content_bundle.listener.article_publish',
            ArticlePublishListener::class
        );

        $definition->addArgument(new Reference('swp_settings.manager.settings'));
        $definition->addArgument(new Reference('swp_multi_tenancy.tenant_context'));
    }
}
