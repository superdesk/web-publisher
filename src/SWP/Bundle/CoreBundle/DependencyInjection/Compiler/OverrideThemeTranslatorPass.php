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

use SWP\Bundle\CoreBundle\Theme\Translation\ThemeAwareTranslator;
use SWP\Bundle\CoreBundle\Translation\MessageFormatter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sylius\Bundle\ThemeBundle\Translation\ThemeAwareTranslator as SyliusThemeAwareTranslator;

final class OverrideThemeTranslatorPass extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->overrideDefinitionClassIfExists(
            $container,
            SyliusThemeAwareTranslator::class,
            ThemeAwareTranslator::class
        );

        $this->overrideDefinitionClassIfExists(
            $container,
            'translator.formatter.default',
            MessageFormatter::class
        );
    }
}
