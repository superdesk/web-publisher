<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\WebRendererBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET")
     */
    public function indexAction()
    {
        $pathBuilder = $this->get('swp_multi_tenancy.path_builder');
        $site = $this->get('swp.repository.site')->find($pathBuilder->build('/'));
        $homepage = $site->getHomepage();


        if (null === $homepage) {
            throw $this->createNotFoundException('No homepage configured!');
        }

        return $this->render('index.html.twig', [
            'page' => $homepage,
        ]);
    }
}
