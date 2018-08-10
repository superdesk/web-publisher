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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Component\Common\Criteria\Criteria;
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
     * @Route("/api/{version}/slideshows/{id}/items/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_slideshow_items", requirements={"id"="\d+"})
     * @Method("GET")
     */
    public function listAction(Request $request, $id)
    {
        $repository = $this->get('swp.repository.slideshow');

        $items = $repository->getPaginatedByCriteria(
            new Criteria([
                'slideshow' => $id,
            ]),
            $request->query->get('sorting', []),
            new PaginationData($request)
        );

        return new ResourcesListResponse($items);
    }
}
