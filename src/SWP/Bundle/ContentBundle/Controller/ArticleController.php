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

use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SWP\Bundle\ContentBundle\Form\Type\ArticleType;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends Controller
{
    /**
     * List all articles for current tenant.
     *
     * ApiDoc(
     *     resource=true,
     *     description="List all articles for current tenant",
     *     statusCodes={
     *         200="Returned on success.",
     *     },
     *     filters={
     *         {"name"="publishedBefore", "dataType"="datetime", "pattern"="Y-m-d h:i:s"},
     *         {"name"="publishedAfter", "dataType"="datetime", "pattern"="Y-m-d h:i:s"},
     *         {"name"="author", "dataType"="string", "pattern"="John Doe | John Doe, Matt Smith"},
     *         {"name"="query", "dataType"="string", "pattern"="Part of title"},
     *         {"name"="sorting", "dataType"="string", "pattern"="[publishedAt|code]=asc|desc"},
     *         {"name"="source", "dataType"="string"}
     *     }
     * )
     *
     * @Operation(
     *     tags={""},
     *     summary="List all articles for current tenant",
     *     @SWG\Parameter(
     *         name="status",
     *         in="query",
     *         description="options: new|published|unpublished|canceled",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="route",
     *         in="query",
     *         description="",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="includeSubRoutes",
     *         in="query",
     *         description="options: new|published|unpublished|canceled",
     *         required=false,
     *         type="boolean"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Unexpected error."
     *     )
     * )
     *
     * @Route("/api/{version}/content/articles/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_list_articles")
     *
     * @param Request $request
     *
     * @return ResourcesListResponse
     *
     * @throws \Exception
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
                'publishedBefore' => $request->query->has('publishedBefore') ? new \DateTime($request->query->get('publishedBefore')) : null,
                'publishedAfter' => $request->query->has('publishedAfter') ? new \DateTime($request->query->get('publishedAfter')) : null,
                'author' => $authors,
                'query' => $request->query->get('query', ''),
                'source' => $request->query->get('source', []),
            ]), $request->query->get('sorting', []), new PaginationData($request));

        return new ResourcesListResponse($articles);
    }

    /**
     * Show single tenant article.
     *
     * @Operation(
     *     tags={""},
     *     summary="Show single tenant article",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success."
     *     )
     * )
     *
     * @Route("/api/{version}/content/articles/{id}", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_show_articles", requirements={"id"=".+"})
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
     * @Operation(
     *     tags={""},
     *     summary="Updates articles",
     *     @SWG\Parameter(
     *         name="status",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="route",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="template_name",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="seoMetadata",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="object (SeoMetadataType)")
     *     ),
     *     @SWG\Parameter(
     *         name="paywallSecured",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when validation failed."
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Returned when unexpected error."
     *     )
     * )
     *
     * @Route("/api/{version}/content/articles/{id}", methods={"PATCH"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_update_articles", requirements={"id"=".+"})
     */
    public function updateAction(Request $request, $id)
    {
        $objectManager = $this->get('swp.object_manager.article');
        $article = $this->findOr404($id);
        $originalArticleStatus = $article->getStatus();

        $form = $this->get('form.factory')->createNamed('', ArticleType::class, $article, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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
     * @Operation(
     *     tags={""},
     *     summary="Deletes articles",
     *     @SWG\Response(
     *         response="204",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when article not found."
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Returned when unexpected error."
     *     )
     * )
     *
     * @Route("/api/{version}/content/articles/{id}", methods={"DELETE"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_delete_articles", requirements={"id"=".+"})
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
