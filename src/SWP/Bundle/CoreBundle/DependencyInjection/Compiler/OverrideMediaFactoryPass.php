<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\DependencyInjection\Compiler;

use SWP\Bundle\CoreBundle\Factory\MediaFactory;
use SWP\Bundle\CoreBundle\Provider\ArticleMediaAssetProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class OverrideMediaFactoryPass extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $mediaDefinition = $this->getDefinitionIfExists($container, 'swp.factory.media');
        $mediaDefinition
            ->setClass(MediaFactory::class)
            ->setArgument(0, new Reference(ArticleMediaAssetProvider::class))
        ;
    }
}
