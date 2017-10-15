<?php

/*
 * This file is part of the Superdesk Web Publisher Analytics Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\AnalyticsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**ProcessArticleMediaListener.php
 * @Route("/statistics")
 */
class StatisticsController extends Controller
{
    /**
     * @Route("/receive")
     *
     * @Method("GET")
     *
     * Receive events
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function receiveAction(Request $request)
    {
        $msg = array('user_id' => 1235, 'image_path' => '/path/to/new/pic.png');
        $this->get('old_sound_rabbit_mq.send_event_producer')->setContentType('application/json')->publish(json_encode($msg, true));

        return new Response();
    }
}
