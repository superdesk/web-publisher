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

use SWP\Bundle\CoreBundle\Service\HealthCheck\HealthCheckServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Route;

/**
 * Class HealthCheckController.
 */
class HealthCheckController extends AbstractController
{
    private HealthCheckServiceInterface $healthCheckService;

    public function __construct(HealthCheckServiceInterface $healthCheckService)
    {
        $this->healthCheckService = $healthCheckService;
    }

    /**
     * @Route("/api/{version}/health", methods={"GET"}, defaults={"version"="v2"}, name="swp_api_health_check")
     */
    public function healthCheckAction(Request $request): JsonResponse
    {
        $detailed = $request->query->getBoolean('detailed', false);
        $service = $request->query->get('service');

        if ($service) {
            $result = $this->healthCheckService->checkService($service);
        } else {
            $result = $this->healthCheckService->checkAll($detailed);
        }

        $statusCode = $result['status'] === 'healthy' ? 200 : 503;

        return new JsonResponse($result, $statusCode);
    }

    /**
     * @Route("/health", methods={"GET"}, name="swp_health_check_simple")
     */
    public function simpleHealthCheckAction(): JsonResponse
    {
        $result = $this->healthCheckService->checkBasic();
        $statusCode = $result['status'] === 'healthy' ? 200 : 503;

        return new JsonResponse($result, $statusCode);
    }
}
