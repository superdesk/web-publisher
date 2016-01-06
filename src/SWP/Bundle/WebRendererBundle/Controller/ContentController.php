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

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Stopwatch\Stopwatch;

class ContentController extends Controller
{
    /**
     * Render content Page.
     */
    public function renderContentPageAction(Request $request, $contentDocument)
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
        $stopwatch = new Stopwatch();
        // start render timer
        $stopwatch->start($view);
        $context = $this->container->get('context');
        $logger = $this->container->get('logger');

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
            $context->registerMeta('article', $article);
        }

<<<<<<< cfbf743d70139569af9be1efbd637f3f3f126d65:src/SWP/Bundle/WebRendererBundle/Controller/ContentController.php
        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');

        return $this->render('article.html.twig', [
            'tenant' => $tenantContext->getTenant(),
        ]);
=======
        $response = $this->render('views/'.$currentPage['templateName']);

        $event = $stopwatch->stop($view);

        // TODO: log the event with the analytics logger here
        $logger->error(print_r($event, true));

        return $response;
>>>>>>> SWP-10: Create Analytics Bundle - initial commit:src/SWP/WebRendererBundle/Controller/ContentController.php
    }

}
