<?php

/**
 * This file is part of the Superdesk Web Publisher Updater Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\UpdaterBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use SWP\UpdaterBundle\DependencyInjection\Compiler\MonologCompilerPass;

class SWPUpdaterBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new MonologCompilerPass());
    }
}
