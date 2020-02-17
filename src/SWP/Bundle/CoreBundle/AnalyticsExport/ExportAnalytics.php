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

use SWP\Bundle\CoreBundle\MessageHandler\Message\MessageInterface;

class ExportAnalytics implements MessageInterface
{
    /** @var string */
    private $start;

    /** @var string */
    private $end;

    /** @var string */
    private $tenantCode;

    /** @var string */
    private $fileName;

    /** @var string */
    private $userEmail;

    /** @var array */
    private $routeIds;

    /** @var array */
    private $authors;

    /** @var string */
    private $term;

    public function __construct(
        string $start,
        string $end,
        string $tenantCode,
        string $fileName,
        string $userEmail,
        array $routeIds,
        array $authors,
        string $term
    ) {
        $this->start = $start;
        $this->end = $end;
        $this->tenantCode = $tenantCode;
        $this->fileName = $fileName;
        $this->userEmail = $userEmail;
        $this->routeIds = $routeIds;
        $this->authors = $authors;
        $this->term = $term;
    }

    public function getStart(): string
    {
        return $this->start;
    }

    public function getEnd(): string
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

    public function getRouteIds(): array
    {
        return array_filter($this->routeIds);
    }

    public function getAuthors(): array
    {
        return array_filter($this->authors);
    }

    public function getTerm(): string
    {
        return $this->term;
    }

    public function getFilters(): array
    {
        return [
            'term' => $this->term,
            'start' => $this->start,
            'end' => $this->end,
            'routes' => array_map('intval', $this->routeIds),
            'authors' => $this->authors,
        ];
    }

    public function toArray(): array
    {
        return [
            'start' => $this->start,
            'end' => $this->end,
            'tenantCode' => $this->tenantCode,
            'fileName' => $this->fileName,
            'userEmail' => $this->userEmail,
            'routeIds' => $this->routeIds,
            'authors' => $this->authors,
            'term' => $this->term,
        ];
    }
}
