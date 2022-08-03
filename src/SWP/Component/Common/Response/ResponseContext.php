<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Common\Response;

class ResponseContext implements ResponseContextInterface
{
    protected $intention;

    protected $statusCode;

    protected $headers;

    protected $clearedCookies = [];

    protected $serializationGroups;

    public function __construct(
        int $statusCode = 200,
        string $intention = ResponseContextInterface::INTENTION_API,
        array $headers = [],
        array $serializationGroups = ['Default', 'api', 'api_route_content']
    ) {
        $this->intention = $intention;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->serializationGroups = $serializationGroups;
    }

    public function getIntention(): string
    {
        return $this->intention;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getClearedCookies(): array
    {
        return $this->clearedCookies;
    }

    public function clearCookie(string $key)
    {
        $this->clearedCookies[] = $key;
    }

    public function getSerializationGroups(): array
    {
        return $this->serializationGroups;
    }

    public function setSerializationGroups(array $serializationGroups): void
    {
        $this->serializationGroups = $serializationGroups;
    }
}
