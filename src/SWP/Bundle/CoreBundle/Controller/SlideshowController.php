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

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Annotation\Route;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
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
     * @ApiDoc(
     *     resource=true,
     *     description="List all slideshows",
     *     statusCodes={
     *         200="Returned on success."
     *     },
     *     filters={
     *         {"name"="sorting", "dataType"="string", "pattern"="[updatedAt]=asc|desc"}
     *     }
     * )
     * @Route("/api/{version}/content/slideshows/{articleId}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_slideshows_list")
     */
    public function listAction(Request $request, string $articleId)
    {
        $repository = $this->get('swp.repository.slideshow');

        $article = $this->findArticleOr404($articleId);

        $slideshows = $repository->getPaginatedByCriteria(new Criteria([
            'article' => $article,
        ]), $request->query->get('sorting', []), new PaginationData($request));

        return new ResourcesListResponse($slideshows);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Get single slideshow",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/content/slideshows/{articleId}/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_get_slideshow", requirements={"id"="\d+"})
     */
    public function getAction($id, string $articleId)
    {
        $article = $this->findArticleOr404($articleId);

        return new SingleResourceResponse($this->findOr404($id, $article));
    }

    private function findOr404($id, ArticleInterface $article)
    {
        if (null === $list = $this->get('swp.repository.slideshow')->findOneBy([
                'id' => $id,
                'article' => $article,
            ])) {
            throw new NotFoundHttpException(sprintf('Slideshow with id "%s" was not found.', $id));
        }

        return $list;
    }

    private function findArticleOr404($id)
    {
        if (null === $article = $this->get('swp.repository.article')->findOneById($id)) {
            throw new NotFoundHttpException(sprintf('Article with id "%s" was not found.', $id));
        }

        return $article;
    }
}
