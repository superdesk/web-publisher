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

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Annotation\Route;
use SWP\Bundle\ContentBundle\Model\SlideshowInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Exception\NotFoundHttpException;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SlideshowItemController extends Controller
{
    /**
     * List all slideshow items.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Lists slideshow items",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Slideshow not found.",
     *         500="Unexpected error."
     *     },
     *     filters={
     *         {"name"="sorting", "dataType"="string", "pattern"="[updatedAt]=asc|desc"}
     *     }
     * )
     * @Route("/api/{version}/content/slideshows/{articleId}/{id}/items/", options={"expose"=true}, defaults={"version"="v1"}, methods={"GET"}, name="swp_api_core_slideshow_items", requirements={"id"="\d+"})
     */
    public function listAction(Request $request, string $articleId, string $id)
    {
        $article = $this->findArticleOr404($articleId);

        $repository = $this->get('swp.repository.slideshow_item');

        $items = $repository->getPaginatedByCriteria(
            new Criteria([
                'article' => $article,
                'slideshow' => $this->findOr404($id),
            ]),
            $request->query->get('sorting', []),
            new PaginationData($request)
        );

        return new ResourcesListResponse($items);
    }

    private function findOr404($id): ?SlideshowInterface
    {
        if (null === $slideshow = $this->get('swp.repository.slideshow')->findOneById($id)) {
            throw new NotFoundHttpException(sprintf('Slideshow with id "%s" was not found.', $id));
        }

        return $slideshow;
    }

    private function findArticleOr404($id)
    {
        if (null === $article = $this->get('swp.repository.article')->findOneById($id)) {
            throw new NotFoundHttpException(sprintf('Article with id "%s" was not found.', $id));
        }

        return $article;
    }
}
