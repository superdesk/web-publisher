<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Factory;

use SWP\Bundle\CoreBundle\Theme\Model\Theme;
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

class ThemeFactory implements ThemeFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(string $name, string $path): ThemeInterface
    {
        return new Theme($name, $path);
    }
}
