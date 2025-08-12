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

namespace SWP\Bundle\CoreBundle\Service\HealthCheck;

/**
 * Interface HealthCheckServiceInterface.
 */
interface HealthCheckServiceInterface
{
    /**
     * Check basic health status (minimal checks for load balancer).
     */
    public function checkBasic(): array;

    /**
     * Check all services health status.
     */
    public function checkAll(bool $detailed = false): array;

    /**
     * Check specific service health status.
     */
    public function checkService(string $service): array;

    /**
     * Get available services for health checks.
     */
    public function getAvailableServices(): array;
}
