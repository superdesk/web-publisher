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

namespace SWP\Bundle\CoreBundle\Theme\Model;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface as BaseThemeInterface;

interface ThemeInterface extends BaseThemeInterface
{
    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return $array
     */
    public function getDefaultTemplates(): array;

    /**
     * @return array
     */
    public function getRoutes(): array;
}
