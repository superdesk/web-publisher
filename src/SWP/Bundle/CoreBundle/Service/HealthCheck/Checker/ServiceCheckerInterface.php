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

namespace SWP\Bundle\CoreBundle\Service\HealthCheck\Checker;

/**
 * Interface ServiceCheckerInterface.
 */
interface ServiceCheckerInterface
{
    /**
     * Check service health.
     *
     * @return array Array with keys: healthy (bool), timestamp (string), and optional error, details
     */
    public function check(): array;

    /**
     * Get service name.
     */
    public function getName(): string;

    /**
     * Get service description.
     */
    public function getDescription(): string;

    /**
     * Check if service is critical for application operation.
     */
    public function isCritical(): bool;
}
