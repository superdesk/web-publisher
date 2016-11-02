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

final class ResourcesListResponse implements ResourcesListResponseInterface
{
    /**
     * @var ResponseContext
     */
    private $responseContext;

    /**
     * @var mixed
     */
    private $resources;

    /**
     * ResourcesListResponse constructor.
     *
     * @param mixed           $resources
     * @param ResponseContext $responseContext
     */
    public function __construct($resources, ResponseContext $responseContext = null)
    {
        if (null === $responseContext) {
            $responseContext = new ResponseContext();
        }

        $this->responseContext = $responseContext;
        $this->resources = $resources;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseContext(): ResponseContext
    {
        return $this->responseContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getResources()
    {
        return $this->resources;
    }
}
