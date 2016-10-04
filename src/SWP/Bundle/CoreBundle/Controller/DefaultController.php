<?php

/**
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
use SWP\Bundle\CoreBundle\Model\HomepageBasedTenantInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET")
     */
    public function indexAction()
    {
        /** @var HomepageBasedTenantInterface $currentTenant */
        $currentTenant = $this->get('swp_multi_tenancy.tenant_context')->getTenant();
        $homepage = $currentTenant->getHomepage();

        if (null !== $homepage) {
            // TODO handle homepage loading here
        }

        $response = $this->render('index.html.twig', [
            'page' => $homepage,
        ]);

        return $response;
    }
}
