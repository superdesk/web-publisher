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

namespace SWP\WebRendererBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContentController extends Controller
{
    /**
     * Render content Page.
     */
    public function renderContentPageAction()
    {
        return $this->renderPage('content');
    }

    /**
     * Render container Page.
     *
     * @param string $contentSlug
     */
    public function renderContainerPageAction($contentSlug)
    {
        return $this->renderPage('container', ['slug' => $contentSlug]);
    }

    /**
     * Render Page.
     *
     * @param string $type
     * @param array  $parameters
     */
    private function renderPage($type, $parameters = [])
    {
        $context = $this->container->get('context');
        $metaLoader = $this->container->get('swp_template_engine_loader_chain');
        $currentPage = $context->getCurrentPage();
        $article = null;

        if ($type == 'content' && !is_null($currentPage['contentPath'])) {
            $article = $metaLoader->load('article', ['contentPath' => $currentPage['contentPath']]);
        } elseif ($type == 'container') {
            $article = $metaLoader->load('article', $parameters);

            if (!$article) {
                throw new NotFoundHttpException('Requested page was not found');
            }
        }

        if ($article) {
            $context->registerMeta('article', $article);
        }

        return $this->render('views/'.$currentPage['templateName']);
    }
}
