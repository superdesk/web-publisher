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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SlideshowControler extends Controller
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
     * @Route("/api/{version}/slideshows/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_slideshows_list")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        $repository = $this->get('swp.repository.slideshow');

        $slideshows = $repository->getPaginatedByCriteria(new Criteria(), $request->query->get('sorting', []), new PaginationData($request));

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
     * @Route("/api/{version}/slideshows/{id}", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_get_slideshow", requirements={"id"="\d+"})
     * @Method("GET")
     */
    public function getAction($id)
    {
        return new SingleResourceResponse($this->findOr404($id));
    }

    private function findOr404($id)
    {
        if (null === $list = $this->get('swp.repository.slideshow')->findOneById($id)) {
            throw new NotFoundHttpException(sprintf('Slideshow with id "%s" was not found.', $id));
        }

        return $list;
    }
}
