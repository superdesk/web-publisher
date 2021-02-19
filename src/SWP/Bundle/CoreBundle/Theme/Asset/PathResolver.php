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

namespace SWP\Bundle\CoreBundle\Theme\Asset;

use Sylius\Bundle\ThemeBundle\Asset\PathResolverInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

class PathResolver implements PathResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve(string $path, string $basePath, ThemeInterface $theme): string
    {
        return str_replace('theme/', 'bundles/_themes/'.$theme->getName().'/', $path);
    }
}
