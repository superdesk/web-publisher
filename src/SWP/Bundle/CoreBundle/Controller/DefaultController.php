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
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET")
     */
    public function indexAction()
    {
        /** @var TenantInterface $currentTenant */
        $currentTenant = $this->get('swp_multi_tenancy.tenant_context')->getTenant();
        $metaFactory = $this->get('swp_template_engine_context.factory.meta_factory');
        $templateEngineContext = $this->get('swp_template_engine_context');
        $route = $currentTenant->getHomepage();

        if (null === $route) {
            $route = $this->get('swp.factory.route')->create();
            $route->setStaticPrefix('/');
            $route->setName('Homepage');
            $route->setType('content');
            $route->setTemplateName('index.html.twig');
        }

        $templateEngineContext->setCurrentPage($metaFactory->create($route));

        return $this->render('index.html.twig');
    }
}
