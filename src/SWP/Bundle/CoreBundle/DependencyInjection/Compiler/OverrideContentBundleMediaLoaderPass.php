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

use SWP\Bundle\CoreBundle\Loader\ArticleMediaLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class OverrideContentBundleMediaLoaderPass extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $mediaLoader = $this->getDefinitionIfExists($container, 'swp_template_engine.loader.media');
        if (null !== $mediaLoader) {
            $mediaLoader
                ->setClass(ArticleMediaLoader::class)
                ->addArgument(new Reference('request_stack'));
        }
    }
}
