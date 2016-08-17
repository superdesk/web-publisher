<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use Symfony\Component\HttpFoundation\Request;

class ContentController extends Controller
{
    public function renderPageAction(Request $request, $contentTemplate, $contentDocument = null, $articleMeta = null)
    {
        if (null == $contentDocument && ($request->attributes->get('type') == RouteInterface::TYPE_COLLECTION)) {
            throw $this->createNotFoundException('Requested page was not found');
        }

        if (null != $articleMeta) {
            $this->container->get('context')->registerMeta('article', $articleMeta);
        }

        return $this->render($contentTemplate);
    }
}
