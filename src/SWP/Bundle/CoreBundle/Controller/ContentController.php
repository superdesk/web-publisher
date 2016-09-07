<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class ContentController.
 */
class ContentController extends Controller
{
    /**
     * @param string $contentTemplate
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderPageAction($contentTemplate)
    {
        return $this->render($contentTemplate);
    }
}
