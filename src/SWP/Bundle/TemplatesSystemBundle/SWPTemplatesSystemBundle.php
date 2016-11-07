<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle;

use SWP\Bundle\TemplatesSystemBundle\DependencyInjection\Compiler\RegisterContainerDataFactory;
use SWP\Bundle\TemplatesSystemBundle\DependencyInjection\Compiler\RegisterContainerFactoryPass;
use SWP\Bundle\TemplatesSystemBundle\DependencyInjection\ContainerBuilder\MetaLoaderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SWPTemplatesSystemBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MetaLoaderCompilerPass());
        $container->addCompilerPass(new RegisterContainerFactoryPass());
        $container->addCompilerPass(new RegisterContainerDataFactory());
    }
}
