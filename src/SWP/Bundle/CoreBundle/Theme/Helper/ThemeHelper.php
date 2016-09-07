<?php

/**
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

namespace SWP\Bundle\CoreBundle\Theme\Helper;

class ThemeHelper implements ThemeHelperInterface
{
    const SUFFIX_SEPARATOR = '@';

    /**
     * @var array
     */
    private $themePaths;

    /**
     * ThemeHelper constructor.
     *
     * @param array $themePaths Paths where themes are stored
     */
    public function __construct(array $themePaths)
    {
        $this->themePaths = $themePaths;
    }

    /**
     * {@inheritdoc}
     */
    public function process(array $themeConfig = [])
    {
        foreach ($this->themePaths as $path) {
            $themesDir = rtrim($path, \DIRECTORY_SEPARATOR).\DIRECTORY_SEPARATOR;

            if (strpos($themeConfig['path'], $themesDir) !== false) {
                $exploded = explode(\DIRECTORY_SEPARATOR, str_replace($themesDir, '', $themeConfig['path']));
                $themeConfig['name'] .= self::SUFFIX_SEPARATOR.$exploded[0];
            }

            continue;
        }

        return $themeConfig;
    }
}
