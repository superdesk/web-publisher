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

use SWP\Component\Common\Response\ResponseContext;

class ResponseContextTest extends \PHPUnit_Framework_TestCase
{
    public function testInitialization()
    {
        $responseContext = new ResponseContext();
        self::assertInstanceOf(ResponseContext::class, $responseContext);

        $responseContext = new ResponseContext(200);
        self::assertInstanceOf(ResponseContext::class, $responseContext);

        $responseContext = new ResponseContext(200, 'test');
        self::assertInstanceOf(ResponseContext::class, $responseContext);
    }

    public function testSettingIntention()
    {
        $responseContext = new ResponseContext(200, 'test');
        self::assertSame('test', $responseContext->getIntention());
    }

    public function testSettingStatusCode()
    {
        $responseContext = new ResponseContext(204);
        self::assertSame(204, $responseContext->getStatusCode());
        self::assertNotEquals(200, $responseContext->getStatusCode());
    }
}
