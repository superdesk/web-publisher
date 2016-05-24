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
namespace SWP\Bundle\WebRendererBundle\Theme\Model;

use SWP\Bundle\WebRendererBundle\Theme\Helper\ThemeHelper;
use Sylius\Bundle\ThemeBundle\Model\Theme as BaseTheme;

class Theme extends BaseTheme
{
    public function __construct($name, $path)
    {
        if ($tempName = strstr($name, ThemeHelper::SUFFIX_SEPARATOR, true)) {
            $name = $tempName;
        }

        parent::__construct($name, $path);
    }
}
