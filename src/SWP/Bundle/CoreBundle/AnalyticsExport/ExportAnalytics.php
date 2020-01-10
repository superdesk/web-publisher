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

namespace SWP\Bundle\CoreBundle\AnalyticsExport;

use DateTimeInterface;

class ExportAnalytics
{
    /** @var DateTimeInterface */
    private $start;

    /** @var DateTimeInterface */
    private $end;

    /** @var string */
    private $tenantCode;

    /** @var string */
    private $fileName;

    /** @var string */
    private $userEmail;

    public function __construct(DateTimeInterface $start, DateTimeInterface $end, string $tenantCode, string $fileName, string $userEmail)
    {
        $this->start = $start;
        $this->end = $end;
        $this->tenantCode = $tenantCode;
        $this->fileName = $fileName;
        $this->userEmail = $userEmail;
    }

    public function getStart(): DateTimeInterface
    {
        return $this->start;
    }

    public function getEnd(): DateTimeInterface
    {
        return $this->end;
    }

    public function getTenantCode(): string
    {
        return $this->tenantCode;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }
}
