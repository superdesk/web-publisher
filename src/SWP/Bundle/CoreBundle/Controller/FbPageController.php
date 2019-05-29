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
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use SWP\Bundle\CoreBundle\Form\Type\FacebookPageType;
use SWP\Bundle\CoreBundle\Model\FacebookPage;
use SWP\Bundle\FacebookInstantArticlesBundle\Model\PageInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FbPageController extends AbstractController
{
    /**
     * @Operation(
     *     tags={"facebook instant articles"},
     *     summary="Lists Facebook Pages",
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
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreBundle\Model\FacebookPage::class, groups={"api"}))
     *         )
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Unexpected error."
     *     )
     * )
     *
     * @Route("/api/{version}/facebook/pages/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_list_facebook_pages")
     */
    public function listAction(Request $request)
    {
        $repository = $this->get('swp.repository.facebook_page');

        $items = $repository->getPaginatedByCriteria(
            new Criteria(),
            $request->query->get('sorting', ['id' => 'asc']),
            new PaginationData($request)
        );

        return new ResourcesListResponse($items);
    }

    /**
     * @Operation(
     *     tags={"facebook instant articles"},
     *     summary="Create Facebook page",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=FacebookPageType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\FacebookPage::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when not valid data."
     *     )
     * )
     *
     * @Route("/api/{version}/facebook/pages/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_create_facebook_pages")
     */
    public function createAction(Request $request)
    {
        /* @var FacebookPage $feed */
        $page = $this->get('swp.factory.facebook_page')->create();
        $form = $this->get('form.factory')->createNamed('', FacebookPageType::class, $page, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->checkIfPageExists($page);
            $this->get('swp.repository.facebook_page')->add($page);

            return new SingleResourceResponse($page, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @Operation(
     *     tags={"facebook instant articles"},
     *     summary="Delete Facebook page",
     *     @SWG\Response(
     *         response="204",
     *         description="Returned on success."
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Unexpected error."
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @SWG\Response(
     *         response="409",
     *         description="Page is used by Instant Articles Feed"
     *     )
     * )
     *
     * @Route("/api/{version}/facebook/pages/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"DELETE"}, name="swp_api_delete_facebook_pages")
     */
    public function deleteAction(int $id): SingleResourceResponseInterface
    {
        $repository = $this->get('swp.repository.facebook_page');
        if (null === $page = $this->get('swp.repository.facebook_page')->findOneBy(['id' => $id])) {
            throw new NotFoundHttpException('There is no Page with provided id!');
        }

        if (null !== $feed = $this->get('swp.repository.facebook_instant_articles_feed')->findOneBy(['facebookPage' => $id])) {
            throw new ConflictHttpException(sprintf('This Page is used by Instant Articles Feed with id: %s!', $feed->getId()));
        }

        $repository->remove($page);

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    private function checkIfPageExists(PageInterface $page): void
    {
        if (null !== $this->get('swp.repository.facebook_page')->findOneBy([
                'pageId' => $page->getPageId(),
            ])
        ) {
            throw new ConflictHttpException('This Page already exists!');
        }
    }
}
