<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\DependencyInjection\Compiler;

use SWP\Bundle\ContentBundle\Processor\EmbeddedImageProcessor;
use SWP\Bundle\CoreBundle\Context\ArticlePreviewContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class OverrideEmbeddedImageProcessorPass extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $embeddedImageProcessor = $this->getDefinitionIfExists($container, EmbeddedImageProcessor::class);
        $embeddedImageProcessor
            ->setClass(\SWP\Bundle\CoreBundle\Processor\EmbeddedImageProcessor::class)
            ->setArgument(2, new Reference(ArticlePreviewContext::class))
            ->setArgument(3, new Reference('swp_settings.manager.settings'))
            ->setArgument(4, new Reference('swp_multi_tenancy.tenant_context'))
        ;
    }
}
