<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use FOS\RestBundle\View\View;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SWP\Bundle\ContentBundle\Form\Type\ArticleType;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;

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
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        $articles = $this->get('swp.repository.article')
            ->getPaginatedByCriteria(new Criteria(), [], new PaginationData($request));

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
        $article = $this->get('swp.provider.article')->getOneById($id);

        if (null === $article) {
            throw new NotFoundHttpException('Article was not found.');
        }

        return $this->handleView(View::create($article, 200));
    }

    /**
     * Updates articles.
     *
     * Possible article statuses are:
     *
     *  * new
     *  * submitted
     *  * published
     *  * unpublished
     *
     * Changing status from any status to `published` will make article visible for every user.
     *
     * Changing status from `published` to any other will make article hidden for user who don't have rights to see unpublished articles.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Updates articles",
     *     statusCodes={
     *         200="Returned on success.",
     *         400="Returned when validation failed.",
     *         500="Returned when unexpected error."
     *     },
     *     input="SWP\Bundle\ContentBundle\Form\Type\ArticleType"
     * )
     * @Route("/api/{version}/content/articles/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_update_articles", requirements={"id"=".+"})
     * @Method("PATCH")
     */
    public function updateAction(Request $request, $id)
    {
        $objectManager = $this->get('swp.object_manager.article');
        $article = $this->findOr404($id);
        $originalArticleStatus = $article->getStatus();

        $form = $this->createForm(ArticleType::class, $article, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->reactOnStatusChange($originalArticleStatus, $article);
            $article->setUpdatedAt(new \DateTime());
            $objectManager->flush();
            $objectManager->refresh($article);

            return $this->handleView(View::create($article, 200));
        }

        return $this->handleView(View::create($form, 500));
    }

    private function reactOnStatusChange($originalArticleStatus, ArticleInterface $article)
    {
        $newArticleStatus = $article->getStatus();
        if ($originalArticleStatus === $newArticleStatus) {
            return;
        }

        $articleService = $this->container->get('swp.service.article');
        switch ($newArticleStatus) {
            case ArticleInterface::STATUS_PUBLISHED:
                $articleService->publish($article);
                break;
            default:
                $articleService->unpublish($article, $newArticleStatus);
                break;
        }
    }

    private function findOr404($id)
    {
        if (null === $article = $this->get('swp.provider.article')->getOneById($id)) {
            throw new NotFoundHttpException('Article was not found.');
        }

        return $article;
    }
}
