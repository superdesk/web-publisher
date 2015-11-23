<?php

/**
 * This file is part of the Superdesk Web Publisher Updater Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\UpdaterBundle\Client;

use PhpSpec\ObjectBehavior;

class GuzzleClientSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('SWP\UpdaterBundle\Client\GuzzleClient');
        $this->shouldImplement('SWP\UpdaterBundle\Client\ClientInterface');
    }

    function let()
    {
    	$config = array('base_uri' => 'http://httpbin.org');
    	$this->beConstructedWith($config);
    }

    function it_should_make_a_call_to_remote_server()
    {
        $this->call('/status/200')->shouldBe('');
    }

    function it_should_throw_exceptions_when_an_error_occurs()
    {
        $this->shouldThrow('\Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException')->duringCall('/status/404');
        $this->shouldThrow('\Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException')->duringCall('/status/500');
    }

    function it_should_be_able_to_return_string()
    {
        $response = $this->call('/headers');
        $response->shouldBeString();
    }

    function it_should_return_full_response_as_array()
    {
        $response = $this->call('/headers', array(), array(), true);
        $response->shouldBeArray();
        $response['status']->shouldBe(200);
        $response['body']->shouldBeString();
        $response['version']->shouldEqual("1.1");
        $response['reason']->shouldBeString();
    }

    function it_should_be_able_to_return_json_format_by_default_when_full_response()
    {
        $response = $this->call('/headers', array(), array(), true);
        $response['headers']->shouldHaveKey('Content-Type');
        $response['headers']['Content-Type']->shouldContain('application/json');
        $response['body']->shouldBeString();
    }

    function it_should_be_able_to_return_xml_format()
    {
        $options = array(
            'options' => array(
                'Content-Type' => 'application/xml'
            )
        );

        $response = $this->call('/xml', array(), $options, true);
        $response['headers']->shouldHaveKey('Content-Type');
        $response['headers']['Content-Type']->shouldContain('application/xml');
        $response['body']->shouldBeString();
    }

    function it_should_be_able_to_accept_query_parameters_as_string()
    {
        $arguments = array(
            'Server=httpbin'
        );

        $response = $this->call('/response-headers', $arguments, array(), true);
        $response['headers']->shouldHaveKey('Content-Type');
        $response['headers']['Content-Type']->shouldContain('application/json');
        $response['body']->shouldBeString();
    }

    function it_should_be_able_to_accept_query_parameters_as_array()
    {
        $arguments = array(
            'Server' => 'httpbin'
        );

        $response = $this->call('/response-headers', $arguments);
        $response->shouldBeString();
    }
}
