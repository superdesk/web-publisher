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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use SWP\Bundle\AnalyticsBundle\Controller\AnalyzedControllerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class DefaultController extends Controller implements AnalyzedControllerInterface
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET")
     */
    public function indexAction()
    {
        // start anayltics
        $logger = $this->container->get('monolog.logger.analytics');
        $stopwatch = new Stopwatch();
        $stopwatch->start('homepage');

        $pathBuilder = $this->get('swp_multi_tenancy.path_builder');
        $manager = $this->get('doctrine_phpcr')->getManager();
        $site = $manager->find('SWP\Bundle\ContentBundle\Document\Site', $pathBuilder->build('/'));
        $homepage = $site->getHomepage();

        if (null === $homepage) {
            throw $this->createNotFoundException('No homepage configured!');
        }

        $tenantContext = $this->get('swp_multi_tenancy.tenant_context');

        $response = $this->render('index.html.twig', [
            'tenant' => $tenantContext->getTenant(),
            'page' => $homepage,
        ]);

        $event = $stopwatch->stop('homepage');
        // TODO: log the event with the analytics logger here
        $logger->debug('This shit took '.$event->getDuration().' milliseconds and used '.$event->getMemory().' bytes of memory');

        return $response;
    }
}
