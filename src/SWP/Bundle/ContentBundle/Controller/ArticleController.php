<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleController extends FOSRestController
{
    /**
     * List all articles for current tenant.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="List all articles for current tenant",
     *     statusCodes={
     *         200="Returned on success.",
     *     }
     * )
     * @Route("/api/{version}/content/articles/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_list_articles")
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function listAction(Request $request)
    {
        $articles = $this->get('knp_paginator')->paginate(
            $this->get('swp.repository.article')->findAllArticles(),
            $request->get('page', 1),
            $request->get('limit', 10)
        );

        $view = View::create($this->get('swp_pagination_rep')->createRepresentation($articles, $request), 200);

        return $this->handleView($view);
    }

    /**
     * Show single tenant article.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Show single tenant article",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/content/articles/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_show_articles", requirements={"id"=".+"})
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function getAction($id)
    {
        $article = $this->get('swp.provider.article')->findOneById($id);

        if (null === $article) {
            throw new NotFoundHttpException('Article was not found.');
        }

        return $this->handleView(View::create($article, 200));
    }
}
