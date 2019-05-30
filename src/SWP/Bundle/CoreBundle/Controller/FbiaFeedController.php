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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use SWP\Bundle\CoreBundle\Form\Type\FacebookInstantArticlesFeedType;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesFeedInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class FbiaFeedController extends AbstractController
{
    /**
     * @Operation(
     *     tags={"facebook instant articles"},
     *     summary="Lists Facebook Instant Articles feeds",
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
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesFeed::class, groups={"api"}))
     *         )
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Unexpected error."
     *     )
     * )
     *
     * @Route("/api/{version}/facebook/instantarticles/feed/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_list_facebook_instant_articles_feed")
     */
    public function listAction(Request $request): ResourcesListResponseInterface
    {
        $repository = $this->get('swp.repository.facebook_instant_articles_feed');

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
     *     summary="Create Facebook Instant Articles feed content list",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=FacebookInstantArticlesFeedType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesFeed::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when not valid data."
     *     )
     * )
     *
     * @Route("/api/{version}/facebook/instantarticles/feed/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_create_facebook_instant_articles_feed")
     */
    public function createAction(Request $request): SingleResourceResponseInterface
    {
        /* @var FacebookInstantArticlesFeedInterface $feed */
        $feed = $this->get('swp.factory.facebook_instant_articles_feed')->create();
        $form = $form = $this->get('form.factory')->createNamed('', FacebookInstantArticlesFeedType::class, $feed, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->checkIfFeedExists($feed->getContentBucket(), $feed->getFacebookPage());
            $this->get('swp.repository.facebook_instant_articles_feed')->add($feed);

            return new SingleResourceResponse($feed, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    private function checkIfFeedExists($contentBucket, $facebookPage)
    {
        if (null !== $this->get('swp.repository.facebook_instant_articles_feed')->findOneBy([
                'contentBucket' => $contentBucket,
                'facebookPage' => $facebookPage,
            ])) {
            throw new ConflictHttpException('Feed for that page and content bucket already exists!');
        }
    }
}
