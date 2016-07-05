<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\WebRendererBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SWP\Bundle\ContentBundle\Model\RouteInterface;


class ContentController extends Controller
{
    public function renderPageAction(Request $request, $contentTemplate, $contentDocument = null, $articleMeta = null)
    {
        if (!$contentDocument && ($request->attributes->get('type') == RouteInterface::TYPE_COLLECTION)) {
            throw new NotFoundHttpException('Requested page was not found');
        }

        if ($articleMeta) {
            $context = $this->container->get('context');
            $context->registerMeta('article', $articleMeta);
        }

        return $this->render($contentTemplate);
    }
}
