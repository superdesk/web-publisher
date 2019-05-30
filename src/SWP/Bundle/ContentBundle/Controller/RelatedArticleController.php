<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Exception\NotFoundHttpException;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RelatedArticleController extends Controller
{
    /**
     * @Operation(
     *     tags={"article"},
     *     summary="List related articles",
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
     *         @Model(type=SWP\Bundle\ContentBundle\Model\RelatedArticle::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Unexpected error."
     *     )
     * )
     */
    public function listAction(Request $request, string $id)
    {
        $article = $this->findOr404($id);

        $repository = $this->get('swp.repository.related_article');

        $items = $repository->getPaginatedByCriteria(
            new Criteria([
                'article' => $article,
            ]),
            $request->query->get('sorting', []),
            new PaginationData($request)
        );

        return new ResourcesListResponse($items);
    }

    private function findOr404(string $id): ArticleInterface
    {
        $article = $this->get('swp.provider.article')->getOneById($id);

        if (null === $article) {
            throw new NotFoundHttpException(sprintf('Article "%s" was not found.', $id));
        }

        return $article;
    }
}
