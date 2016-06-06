<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\WebRendererBundle\Theme\Helper;

interface ThemeHelperInterface
{
    /**
     * Processes theme.
     *
     * @param array $themeConfig Theme config from json file.
     *
     * @return array
     */
    public function process(array $themeConfig = []);
}
