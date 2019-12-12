<?php

declare(strict_types=1);

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

use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use SWP\Bundle\RedirectRouteBundle\Form\RedirectRouteType;
use SWP\Bundle\RedirectRouteBundle\Model\RedirectRouteInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RedirectRouteController extends AbstractController
{
    /**
     * Lists redirect routes.
     *
     * @Operation(
     *     tags={"redirect_route"},
     *     summary="Lists redirect routes",
     *     @SWG\Parameter(
     *         name="sorting",
     *         in="query",
     *         description="example: [createdAt]=asc|desc",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\RedirectRoute\Model\RedirectRoute::class, groups={"api"}))
     *         )
     *     )
     * )
     *
     * @Route("/api/{version}/redirects/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_list_redirect_routes")
     */
    public function listAction(Request $request)
    {
        $redirectRouteRepository = $this->get('swp.repository.redirect_route');

        $redirectRoutes = $redirectRouteRepository->getPaginatedByCriteria(new Criteria(), $request->query->get('sorting', []), new PaginationData($request));

        return new ResourcesListResponse($redirectRoutes);
    }

    /**
     * @Operation(
     *     tags={"redirect_route"},
     *     summary="Delete redirect route",
     *     @SWG\Response(
     *         response="204",
     *         description="Returned on success."
     *     )
     * )
     *
     * @Route("/api/{version}/redirects/{id}", methods={"DELETE"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_delete_redirect_route", requirements={"id"="\d+"})
     *
     * @return Response
     */
    public function deleteAction(int $id): SingleResourceResponseInterface
    {
        $objectManager = $this->get('swp.object_manager.redirect_route');
        $redirectRoute = $this->findOr404($id);

        $objectManager->remove($redirectRoute);
        $objectManager->flush();

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    /**
     * @Operation(
     *     tags={"redirect_route"},
     *     summary="Create new redirect route",
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="",
     *         @SWG\Schema(
     *             ref=@Model(type=\SWP\Bundle\RedirectRouteBundle\Form\RedirectRouteType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\RedirectRouteBundle\Model\RedirectRoute::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when not valid data."
     *     )
     * )
     *
     * @Route("/api/{version}/redirects/", methods={"POST"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_create_redirect_route")
     */
    public function createAction(Request $request): SingleResourceResponseInterface
    {
        $redirectRoute = $this->get('swp.factory.redirect_route')->create();
        $form = $this->get('form.factory')->createNamed('', RedirectRouteType::class, $redirectRoute, ['method' => $request->getMethod()]);

        $form->handleRequest($request);

        if (null === $redirectRoute->getRouteSource()) {
            $this->ensureRedirectRouteExists($redirectRoute->getRouteName());
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $redirectRoute->getRouteSource()) {
                $redirectRoute->setStaticPrefix($redirectRoute->getRouteName());
            } else {
                $redirectRoute->setStaticPrefix($redirectRoute->getRouteSource()->getStaticPrefix());
            }

            $this->get('swp.repository.redirect_route')->add($redirectRoute);

            return new SingleResourceResponse($redirectRoute, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @Operation(
     *     tags={"redirect_route"},
     *     summary="Update redirect route",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="",
     *         required=true,
     *         @SWG\Schema(
     *             ref=@Model(type=\SWP\Bundle\RedirectRouteBundle\Form\RedirectRouteType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\RedirectRouteBundle\Model\RedirectRoute::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when not valid data."
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when not found."
     *     ),
     *     @SWG\Response(
     *         response="409",
     *         description="Returned on conflict."
     *     )
     * )
     *
     * @Route("/api/{version}/redirects/{id}", methods={"PATCH"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_update_redirect_route", requirements={"id"="\d+"})
     */
    public function updateAction(Request $request, int $id): SingleResourceResponseInterface
    {
        $objectManager = $this->get('swp.object_manager.redirect_route');
        $redirectRoute = $this->findOr404($id);
        $form = $this->get('form.factory')->createNamed('', RedirectRouteType::class, $redirectRoute, ['method' => $request->getMethod()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $objectManager->flush();

            return new SingleResourceResponse($redirectRoute, new ResponseContext(200));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    private function findOr404(int $id): RedirectRouteInterface
    {
        if (null === $redirectRoute = $this->get('swp.repository.redirect_route')->findOneById($id)) {
            throw new NotFoundHttpException('Redirect route was not found.');
        }

        return $redirectRoute;
    }

    private function ensureRedirectRouteExists(string $name): void
    {
        if (null !== $this->get('swp.repository.redirect_route')->findOneBy(['routeName' => $name])) {
            throw new ConflictHttpException(sprintf('Redirect route "%s" already exists!', $name));
        }
    }
}
