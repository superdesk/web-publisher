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

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;

interface AnalyticsReportInterface extends FileInterface, TenantAwareInterface
{
    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_ERRORED = 'errored';

    public function setUser(UserInterface $user): void;

    public function getUser(): UserInterface;

    public function getStatus(): string;

    public function setStatus(string $status): void;

    public function getFilters(): array;

    public function setFilters(array $filters): void;
}
