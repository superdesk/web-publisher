<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Component\Routing\Annotation\Route;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FbiaArticleController extends Controller
{
    /**
     * @Operation(
     *     tags={"facebook instant articles"},
     *     summary="Lists Facebook Instant Articles submitted articles",
     *     @SWG\Parameter(
     *         name="sorting",
     *         in="query",
     *         description="example: [updatedAt]=asc|desc",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesArticle::class, groups={"api"}))
     *         )
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Unexpected error."
     *     )
     * )
     *
     * @Route("/api/{version}/facebook/instantarticles/articles/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_list_facebook_instant_articles_articles")
     */
    public function listAction(Request $request): ResourcesListResponseInterface
    {
        $repository = $this->get('swp.repository.facebook_instant_articles_article');

        $items = $repository->getPaginatedByCriteria(
            new Criteria(),
            $request->query->get('sorting', ['createdAt' => 'desc']),
            new PaginationData($request)
        );

        return new ResourcesListResponse($items);
    }

    /**
     * @Operation(
     *     tags={"facebook instant articles"},
     *     summary="Updates status of submitted Instant Article",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesArticle::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Unexpected error."
     *     )
     * )
     *
     * @Route("/api/{version}/facebook/instantarticles/articles/{submissionId}", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_facebook_instant_articles_articles_update")
     */
    public function updateSubmissionAction(string $submissionId): SingleResourceResponseInterface
    {
        $instantArticlesService = $this->get('swp.facebook.service.instant_articles');
        $instantArticle = $instantArticlesService->updateSubmissionStatus($submissionId);

        return new SingleResourceResponse($instantArticle);
    }
}
