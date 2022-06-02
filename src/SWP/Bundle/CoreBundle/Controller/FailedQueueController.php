<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use SWP\Bundle\CoreBundle\Provider\FailedEntriesProvider;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FailedQueueController extends AbstractController {
  /**
   * @Route("/api/{version}/failed_queue/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_list_failed_queue")
   */
  public function listAction(Request $request, FailedEntriesProvider $failedEntriesProvider) {
    $max = $request->query->getInt('limit', 50);

    return new SingleResourceResponse($failedEntriesProvider->getFailedEntries($max));
  }
}
