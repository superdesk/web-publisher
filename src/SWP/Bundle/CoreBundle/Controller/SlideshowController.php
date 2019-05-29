<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SlideshowController extends Controller
{
    /**
     * @Operation(
     *     tags={"slideshow"},
     *     summary="List all slideshows",
     *     @SWG\Parameter(
     *         name="sorting",
     *         in="query",
     *         description="todo",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreBundle\Model\Slideshow::class, groups={"api"}))
     *         )
     *     )
     * )
     *
     * @Route("/api/{version}/content/slideshows/{articleId}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_slideshows_list")
     */
    public function listAction(Request $request, string $articleId): ResourcesListResponseInterface
    {
        $repository = $this->get('swp.repository.slideshow');

        $article = $this->findArticleOr404($articleId);

        $slideshows = $repository->getPaginatedByCriteria(new Criteria([
            'article' => $article,
        ]), $request->query->get('sorting', []), new PaginationData($request));

        return new ResourcesListResponse($slideshows);
    }

    /**
     * @Operation(
     *     tags={"slideshow"},
     *     summary="Get single slideshow",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\Slideshow::class, groups={"api"})
     *     )
     * )
     *
     * @Route("/api/{version}/content/slideshows/{articleId}/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_get_slideshow", requirements={"id"="\d+"})
     */
    public function getAction($id, string $articleId): SingleResourceResponseInterface
    {
        $article = $this->findArticleOr404($articleId);

        if (null === $list = $this->get('swp.repository.slideshow')->findOneBy([
                'id' => $id,
                'article' => $article,
            ])) {
            throw new NotFoundHttpException(sprintf('Slideshow with id "%s" was not found.', $id));
        }

        return new SingleResourceResponse($list);
    }

    private function findArticleOr404($id)
    {
        if (null === $article = $this->get('swp.repository.article')->findOneById($id)) {
            throw new NotFoundHttpException(sprintf('Article with id "%s" was not found.', $id));
        }

        return $article;
    }
}
