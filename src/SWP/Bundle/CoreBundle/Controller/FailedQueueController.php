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

use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use SWP\Bundle\CoreBundle\Provider\FailedEntriesProvider;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class FailedQueueController extends AbstractController
{
    /**
     * @Operation(
     *     tags={"failed_queue"},
     *     summary="Lists analytics reports",
     *     @SWG\Parameter(
     *         name="limit",
     *         in="query",
     *         description="example: limit=5",
     *         default=50,
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreRoute\Model\FailedEntry::class, groups={"api"}))
     *         )
     *     )
     * )
     *
     * @Route("/api/{version}/failed_queue/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_list_failed_queue")
     */
    public function listAction(Request $request, FailedEntriesProvider $failedEntriesProvider)
    {
        $max = (int) $request->query->get('limit', 50);

        return new SingleResourceResponse($failedEntriesProvider->getFailedEntries($max));
    }
}
