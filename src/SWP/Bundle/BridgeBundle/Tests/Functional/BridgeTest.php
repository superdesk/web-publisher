<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\BridgeBundle\Tests\Functional;

class BridgeTest extends WebTestCase
{
    const TEST_CASE_NAME = 'Bridge';

    /**
     * @expectedException \Superdesk\ContentApiSdk\Exception\ContentApiException
     * @dataProvider getConfigs
     */
    public function testIndexForCallToInvalidEndpoints($config)
    {
        $client = $this->createClient(['test_case' => self::TEST_CASE_NAME, 'root_config' => $config]);

        $client->request('GET', '/bridge/invalid_endpoint/');
    }

    public function getConfigs()
    {
        return [
            ['config.yml'],
        ];
    }

    public static function setUpBeforeClass()
    {
        parent::deleteTmpDir(self::TEST_CASE_NAME);
    }

    public static function tearDownAfterClass()
    {
        parent::deleteTmpDir(self::TEST_CASE_NAME);
    }
}
