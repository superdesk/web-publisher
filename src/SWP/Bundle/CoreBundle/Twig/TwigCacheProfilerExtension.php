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

namespace SWP\Bundle\CoreBundle\Twig;

use Asm89\Twig\CacheExtension\Extension;
use EmanueleMinotto\TwigCacheBundle\Twig\ProfilerExtension;

/**
 * Class TwigCacheProfilerExtension
 * It's needed to cover that issue: https://github.com/asm89/twig-cache-extension/pull/46.
 */
class TwigCacheProfilerExtension extends ProfilerExtension
{
    /**
     * @return string
     */
    public function getName()
    {
        return Extension::class;
    }
}
