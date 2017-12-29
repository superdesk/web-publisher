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

namespace SWP\Bundle\CoreBundle\Theme\Processor;

use SWP\Bundle\CoreBundle\Theme\Model\ThemeInterface;

/**
 * Interface ThemeUploaderInterface.
 */
interface RequiredDataProcessorInterface
{
    /**
     * @param ThemeInterface $theme
     */
    public function processTheme(ThemeInterface $theme): void;
}
