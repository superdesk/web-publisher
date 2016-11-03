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

use SWP\Bundle\CoreBundle\Response\ResponseContext;
use SWP\Bundle\CoreBundle\Response\ResponseContextInterface;
use SWP\Bundle\CoreBundle\Response\SingleResourceResponse;

class SingleResourceResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testInitialization()
    {
        $singleResourceResponse = new SingleResourceResponse([]);
        self::assertInstanceOf(SingleResourceResponse::class, $singleResourceResponse);

        $singleResourceResponse = new SingleResourceResponse([], new ResponseContext());
        self::assertInstanceOf(SingleResourceResponse::class, $singleResourceResponse);
    }

    public function testSettingResource()
    {
        $singleResourceResponse = new SingleResourceResponse([1, 2, 3]);
        self::assertSame([1, 2, 3], $singleResourceResponse->getResource());
    }

    public function testSettingCustomContext()
    {
        $context = new ResponseContext(500, ResponseContextInterface::INTENTION_API);
        $singleResourceResponse = new SingleResourceResponse(
            [1, 2, 3],
            $context
        );

        self::assertSame($context, $singleResourceResponse->getResponseContext());
    }
}
