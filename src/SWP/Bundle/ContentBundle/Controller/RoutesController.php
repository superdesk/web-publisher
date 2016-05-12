<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;

class RoutesController extends FOSRestController
{
    /**
     * Lists current tenant routes.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Lists current tenant routes",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/content/routes/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_list_routes")
     * @Method("GET")
     *
     * Cache(expires="10 minutes", public=true)
     */
    public function listAction(Request $request)
    {
        $manager = $this->get('doctrine_phpcr')->getManager();
        $basepath = $this->get('swp_multi_tenancy.path_builder')
            ->build($this->getParameter('swp_multi_tenancy.persistence.phpcr.route_basepaths')[0]);

        $routes = $this->get('knp_paginator')
            ->paginate($manager->find('SWP\Bundle\ContentBundle\Document\Route', $basepath)
            ->getRouteChildren());

        return $this->handleView(View::create($this->get('swp_pagination_rep')->createRepresentation($routes, $request), 200));
    }

    public function createAction(Request $request)
    {
        $manager = $this->get('doctrine_phpcr')->getManager();
        $basepath = $this->get('swp_multi_tenancy.path_builder')
            ->build($this->getParameter('swp_multi_tenancy.persistence.phpcr.route_basepaths')[0]);


    }
}
