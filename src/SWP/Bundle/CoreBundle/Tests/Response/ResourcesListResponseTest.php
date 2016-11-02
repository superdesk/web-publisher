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

namespace SWP\Bundle\CoreBundle\Tests\Response;

use SWP\Bundle\CoreBundle\Response\ResourcesListResponse;
use SWP\Bundle\CoreBundle\Response\ResponseContext;
use SWP\Bundle\CoreBundle\Response\ResponseContextInterface;

class ResourcesListResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testInitialization()
    {
        $resourcesListResponse = new ResourcesListResponse([]);
        self::assertInstanceOf(ResourcesListResponse::class, $resourcesListResponse);

        $resourcesListResponse = new ResourcesListResponse([], new ResponseContext());
        self::assertInstanceOf(ResourcesListResponse::class, $resourcesListResponse);
    }

    public function testSettingResources()
    {
        $resourcesListResponse = new ResourcesListResponse([1, 2, 3]);
        self::assertSame([1, 2, 3], $resourcesListResponse->getResources());
    }

    public function testSettingCustomContext()
    {
        $context = new ResponseContext(500, ResponseContextInterface::INTENTION_API);
        $resourcesListResponse = new ResourcesListResponse(
            [1, 2, 3],
            $context
        );

        self::assertSame($context, $resourcesListResponse->getResponseContext());
    }
}
