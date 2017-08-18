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

namespace SWP\Bundle\CoreBundle\Fragment;

use Symfony\Component\HttpKernel\Fragment\AbstractSurrogateFragmentRenderer;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;

/**
 * Class ExternalEsiFragmentRenderer.
 */
class ExternalEsiFragmentRenderer extends AbstractSurrogateFragmentRenderer implements FragmentRendererInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'external_esi';
    }
}
