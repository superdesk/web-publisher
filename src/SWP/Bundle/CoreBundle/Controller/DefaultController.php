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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET|POST")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        /** @var TenantInterface $currentTenant */
        $currentTenant = $this->get('swp_multi_tenancy.tenant_context')->getTenant();
        $metaFactory = $this->get('swp_template_engine_context.factory.meta_factory');
        $templateEngineContext = $this->get('swp_template_engine_context');
        $route = $currentTenant->getHomepage();

        if (null === $route) {
            /** @var RouteInterface $route */
            $route = $this->get('swp.factory.route')->create();
            $route->setStaticPrefix('/');
            $route->setName('Homepage');
            $route->setType('content');
            $route->setTemplateName('index.html.twig');
            $route->setCacheTimeInSeconds(360);
            $request->attributes->set(DynamicRouter::ROUTE_KEY, $route);
        }

        $templateEngineContext->setCurrentPage($metaFactory->create($route));

        return $this->render('index.html.twig');
    }
}
