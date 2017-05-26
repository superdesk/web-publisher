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

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SWP\Bundle\ContentBundle\Form\Type\ArticleType;

class ArticleController extends Controller
{
    /**
     * List all articles for current tenant.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="List all articles for current tenant",
     *     statusCodes={
     *         200="Returned on success.",
     *     },
     *     filters={
     *         {"name"="status", "dataType"="string", "pattern"="new|published|unpublished|canceled"},
     *         {"name"="route", "dataType"="integer"},
     *         {"name"="includeSubRoutes", "dataType"="boolean"},
     *         {"name"="publishedBefore", "dataType"="datetime", "pattern"="Y-m-d h:i:s"},
     *         {"name"="publishedAfter", "dataType"="datetime", "pattern"="Y-m-d h:i:s"},
     *         {"name"="author", "dataType"="string", "pattern"="John Doe | John Doe, Matt Smith"},
     *         {"name"="query", "dataType"="string", "pattern"="Part of title"},
     *         {"name"="sorting", "dataType"="string", "pattern"="[publishedAt|code]=asc|desc"},
     *         {"name"="source", "dataType"="string"}
     *     }
     * )
     * @Route("/api/{version}/content/articles/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_list_articles")
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     *
     * @param Request $request
     *
     * @return ResourcesListResponse
     */
    public function listAction(Request $request)
    {
        $authors = '';
        if (null !== $request->query->get('author', null)) {
            $authors = explode(', ', $request->query->get('author'));
        }

        if ($request->query->get('route', false) && $request->query->get('includeSubRoutes', false)) {
            $routeObject = $this->get('swp.provider.route')->getOneById($request->query->get('route'));

            if (null !== $routeObject) {
                $ids = [$routeObject->getId()];
                foreach ($routeObject->getChildren() as $child) {
                    $ids[] = $child->getId();
                }
                $request->query->set('route', $ids);
            }
        }

        $articles = $this->get('swp.repository.article')
            ->getPaginatedByCriteria(new Criteria([
                'status' => $request->query->get('status', ''),
                'route' => $request->query->get('route', ''),
                'publishedBefore' => $request->query->has('publishedBefore') ? new \DateTime($request->query->get('publishedBefore')) : '',
                'publishedAfter' => $request->query->has('publishedAfter') ? new \DateTime($request->query->get('publishedAfter')) : '',
                'author' => $authors,
                'query' => $request->query->get('query', ''),
                'source' => $request->query->get('source', ''),
            ]), $request->query->get('sorting', []), new PaginationData($request));

        return new ResourcesListResponse($articles);
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

        return new SingleResourceResponse($article);
    }

    /**
     * Updates articles.
     *
     * Possible article statuses are:
     *
     *  * new
     *  * published
     *  * unpublished
     *  * canceled
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
            $this->get('swp.service.article')->reactOnStatusChange($originalArticleStatus, $article);
            $objectManager->flush();
            $objectManager->refresh($article);

            return new SingleResourceResponse($article);
        }

        return new SingleResourceResponse($form, new ResponseContext(500));
    }

    /**
     * Delete Article.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Deletes articles",
     *     statusCodes={
     *         204="Returned on success.",
     *         404="Returned when article not found.",
     *         500="Returned when unexpected error."
     *     }
     * )
     * @Route("/api/{version}/content/articles/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_delete_articles", requirements={"id"=".+"})
     * @Method("DELETE")
     *
     * @param int $id
     *
     * @return SingleResourceResponse
     */
    public function deleteAction($id)
    {
        $objectManager = $this->get('swp.object_manager.article');
        $objectManager->remove($this->findOr404($id));
        $objectManager->flush();

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    private function findOr404($id)
    {
        if (null === $article = $this->get('swp.provider.article')->getOneById($id)) {
            throw new NotFoundHttpException('Article was not found.');
        }

        return $article;
    }
}
