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

namespace SWP\Bundle\CoreBundle\Response;

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
     * ResponseContext constructor.
     *
     * @param string $intention
     * @param int    $statusCode
     */
    public function __construct(int $statusCode = 200, string $intention = self::INTENTION_API)
    {
        $this->intention = $intention;
        $this->statusCode = $statusCode;
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
}
