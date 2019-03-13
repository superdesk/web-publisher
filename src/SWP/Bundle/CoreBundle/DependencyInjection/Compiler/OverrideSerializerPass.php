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

use SWP\Bundle\CoreBundle\Serializer\CachedJMSSerializer;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideSerializerPass extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $this->getDefinitionIfExists($container, 'swp.serializer');
        if (null !== $definition) {
            $definition
                ->setClass(CachedJMSSerializer::class);
        }
    }
}
