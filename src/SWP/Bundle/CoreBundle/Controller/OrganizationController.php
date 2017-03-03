<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class OrganizationController extends Controller
{
    /**
     * List all organization's articles.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="List all organization's articles",
     *     statusCodes={
     *         200="Returned on success.",
     *         500="Returned when unexpected error occurred."
     *     }
     * )
     * @Route("/api/{version}/organizations/{id}/articles/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_list_organization_articles")
     * @Method("GET")
     *
     * @Cache(expires="10 minutes", public=true)
     */
    public function articlesAction(Request $request, int $id)
    {
        $repository = $this->get('swp.repository.article');

        $entityManager = $this->get('doctrine.orm.entity_manager');
        $entityManager->getFilters()->disable('tenantable');

        $items = $repository->getPaginatedByCriteria(
            new Criteria([
                'organization' => $id,
            ]),
            [],
            new PaginationData($request)
        );

        $entityManager->getFilters()->enable('tenantable');

        return new ResourcesListResponse($items);
    }
}
