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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentController extends Controller
{
    /**
     * Render content Page.
     */
    public function renderContentPageAction(Request $request, $contentDocument = null)
    {
        return $this->renderPage('content', ['article' => $contentDocument]);
    }

    /**
     * Render container Page.
     */
    public function renderContainerPageAction(Request $request, $slug)
    {
        return $this->renderPage('container', ['slug' => $slug]);
    }

    /**
     * Render Page.
     *
     * @param string $type
     * @param array  $parameters
     */
    private function renderPage($type, $parameters = [])
    {
        $metaLoader = $this->container->get('swp_template_engine_loader_chain');
        $article = null;

        if ($type == 'content' && !is_null($parameters['article'])) {
            $article = $metaLoader->load('article', ['article' => $parameters['article']]);
        } elseif ($type == 'container') {
            $article = $metaLoader->load('article', $parameters);

            if (!$article) {
                throw new NotFoundHttpException('Requested page was not found');
            }
        }

        if ($article) {
            $context = $this->container->get('context');
            $context->registerMeta('article', $article);
        }

        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');

        $response = $this->render('article.html.twig', [
            'tenant' => $tenantContext->getTenant(),
        ]);

        return $response;
    }
}
