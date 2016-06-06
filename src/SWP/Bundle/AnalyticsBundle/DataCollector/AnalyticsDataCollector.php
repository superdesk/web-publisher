<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\AnalyticsBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SWP\Bundle\AnalyticsBundle\Model\AnalyticsLog;

class AnalyticsDataCollector extends DataCollector
{
    protected $container;

    protected $em;

    public function __construct($container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getManager();
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $previous = array();

        try {
            $latest = $this->em->getRepository('SWP\Bundle\AnalyticsBundle\Model\AnalyticsLog')
                ->getLatest(1)
                ->getSingleResult();

            $logs = $this->em->getRepository('SWP\Bundle\AnalyticsBundle\Model\AnalyticsLog')
                ->getLatestByUri($latest->getUri())
                ->getResult();

            foreach ($logs as $log) {
                array_push($previous, array(
                    'created' => $log->getCreated()->format('Y-m-d H:i:s'),
                    'duration' => $log->getDuration(),
                    'memory' => (($log->getMemory() / 1024) / 1024),
                    'uri' => $log->getUri()
                ));
            }
        } catch (\Exception $e) {
            // no analytics logs, so just return emtpy array
            $this->data = array('context' => array());
            return;
        }

        $data = array(
            'latest' => array(
                'created' => $latest->getCreated()->format('Y-m-d H:i:s'),
                'duration' => $latest->getDuration(),
                'memory' => (($latest->getMemory() / 1024) / 1024),
                'uri' => $latest->getUri()),
            'previous' => $previous,
        );
        $this->data = array(
            'context' => $data,
        );
    }

    public function getContext()
    {
        return $this->data['context'];
    }

    public function getName()
    {
        return 'analytics_collector';
    }
}
