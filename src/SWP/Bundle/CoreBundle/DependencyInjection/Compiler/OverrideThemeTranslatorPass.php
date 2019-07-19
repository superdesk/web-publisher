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

use SWP\Bundle\CoreBundle\Theme\Provider\CachedThemeAssetProviderInterface;
use SWP\Bundle\CoreBundle\Theme\Provider\ThemeAssetProviderInterface;
use SWP\Bundle\CoreBundle\Theme\Translation\S3TranslationFilesFinder;
use SWP\Bundle\CoreBundle\Theme\Translation\ThemeAwareTranslator;
use SWP\Bundle\CoreBundle\Translation\MessageFormatter;
use SWP\Bundle\CoreBundle\Translation\S3XliffLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class OverrideThemeTranslatorPass extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->overrideDefinitionClassIfExists(
            $container,
            'sylius.theme.translation.theme_aware_translator',
            ThemeAwareTranslator::class
        );

        $this->overrideDefinitionClassIfExists(
            $container,
            'translator.formatter.default',
            MessageFormatter::class
        );

        $filesFinderDefinition = $this->getDefinitionIfExists($container, 'sylius.theme.translation.files_finder');
        if (null !== $filesFinderDefinition) {
            $filesFinderDefinition->setClass(S3TranslationFilesFinder::class);
            $filesFinderDefinition->setArgument(0, new Reference(ThemeAssetProviderInterface::class));
        }

        $xliffLoaderDefinition = $this->getDefinitionIfExists($container, 'translation.loader.xliff');
        if (null !== $xliffLoaderDefinition) {
            $xliffLoaderDefinition->setClass(S3XliffLoader::class);
            $xliffLoaderDefinition->setArgument(0, new Reference(CachedThemeAssetProviderInterface::class));
        }
    }
}
