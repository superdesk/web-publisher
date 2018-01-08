<?php

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

use Symfony\Component\DependencyInjection\ContainerBuilder;

final class OverrideHateoasTwigHelperPass extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $twig = $this->getDefinitionIfExists($container, 'twig');
        $methodCalls = $twig->getMethodCalls();
        foreach ($twig->getMethodCalls() as $key => $references) {
            if ('addExtension' === $references[0] && 'hateoas.twig.link' === $references[1][0]->__toString()) {
                unset($methodCalls[$key]);
            }
        }
        $twig->setMethodCalls($methodCalls);
        $hateoas = $this->getDefinitionIfExists($container, 'hateoas.twig.link');
        $hateoas->clearTags();
        $container->removeDefinition('hateoas.twig.link');
    }
}
