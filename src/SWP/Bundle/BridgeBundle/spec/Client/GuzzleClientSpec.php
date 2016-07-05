<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge for the Content API.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\BridgeBundle\Client;

use PhpSpec\ObjectBehavior;

class GuzzleClientSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('\SWP\Bundle\BridgeBundle\Client\GuzzleClient');
        $this->shouldImplement('\Superdesk\ContentApiSdk\Client\ClientInterface');
    }

    public function its_method_make_call_should_make_a_generic_http_request()
    {
        $response = $this->makeCall('http://httpbin.org/status/200', []);
        $response->shouldHaveKey('headers');
        $response->shouldHaveKeyWithValue('status', 200);
        $response->shouldHaveKeyWithValue('body', '');
    }

    public function its_method_make_call_should_set_correct_status_codes()
    {
        $this->makeCall('http://httpbin.org/status/404')->shouldHaveKeyWithValue('status', 404);
        $this->makeCall('http://httpbin.org/status/500')->shouldHaveKeyWithValue('status', 500);
    }

    public function its_method_make_call_should_send_headers()
    {
        $headers = [
            'Authorization: some authorization token',
            'X-Custom-Header: Blaat blaat',
        ];
        $response = $this->makeCall('http://httpbin.org/headers', $headers);
        $response->shouldHaveKey('body');

        foreach ($headers as $header) {
            list($key, $value) = explode(': ', $header);
            $response['body']->shouldMatch(sprintf('/%s/i', $key));
            $response['body']->shouldMatch(sprintf('/%s/i', $value));
        }
    }

    public function its_method_make_call_should_support_post_requests()
    {
        $postData = 'some random post data';
        $response = $this->makeCall(
            'http://httpbin.org/post',
            [],
            [],
            'POST',
            $postData
        );
        $response->shouldHaveKey('body');
        $response['body']->shouldMatch(sprintf('/%s/i', $postData));
    }

    public function it_should_throw_an_exception_when_an_error_occurs()
    {
        $this->shouldThrow('\Superdesk\ContentApiSdk\Exception\ClientException')->duringMakeCall('some random url that is invalid');
    }
}
