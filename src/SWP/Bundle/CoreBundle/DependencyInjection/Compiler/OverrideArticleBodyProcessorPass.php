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

use SWP\Bundle\CoreBundle\Processor\ArticleBodyProcessor;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class OverrideArticleBodyProcessorPass extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $mediaManager = $this->getDefinitionIfExists($container, 'swp_content_bundle.processor.article_body');
        $mediaManager
            ->setClass(ArticleBodyProcessor::class)
        ;
    }
}
