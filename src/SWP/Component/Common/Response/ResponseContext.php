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
    /**
     * @var string
     */
    protected $intention;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var array
     */
    protected $clearedCookies = [];

    /**
     * ResponseContext constructor.
     *
     * @param int    $statusCode
     * @param string $intention
     * @param array  $headers
     */
    public function __construct(
        int $statusCode = 200,
        string $intention = ResponseContextInterface::INTENTION_API,
        array $headers = []
    ) {
        $this->intention = $intention;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function getIntention(): string
    {
        return $this->intention;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function getClearedCookies(): array
    {
        return $this->clearedCookies;
    }

    /**
     * {@inheritdoc}
     */
    public function clearCookie(string $key)
    {
        $this->clearedCookies[] = $key;
    }
}
