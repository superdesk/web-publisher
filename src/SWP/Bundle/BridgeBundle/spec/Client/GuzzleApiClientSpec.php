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
use Prophecy\Argument;
use Superdesk\ContentApiSdk\Api\Authentication\OAuthPasswordAuthentication;
use Superdesk\ContentApiSdk\Api\Request;
use Superdesk\ContentApiSdk\Client\ClientInterface;

class GuzzleApiClientSpec extends ObjectBehavior
{
    public function let(
        ClientInterface $client,
        OAuthPasswordAuthentication $authentication,
        Request $request
    ) {
        $this->beConstructedWith($client, $authentication);

        $baseUrl = 'http://httpbin.org/';
        $fullUrl = sprintf('%s/%s', $baseUrl, 'status/200');
        $headers = ['Accept' => 'application/json'];
        $newHeaders = ['Accept' => 'application/json', 'Authorization' => 'OAuth2 some_access_token'];
        $request->getBaseUrl()->willReturn($baseUrl);
        $request->getFullUrl()->willReturn($fullUrl);
        $request->getHeaders()->willReturn($headers);
        $request->setHeaders($newHeaders)->willReturn($request);
        $authentication->setBaseUrl($baseUrl)->willReturn($authentication);
        $authentication->getAccessToken()->willReturn('some_access_token');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('\SWP\Bundle\BridgeBundle\Client\GuzzleApiClient');
        $this->shouldImplement('\Superdesk\ContentApiSdk\Client\ApiClientInterface');
    }

    public function it_should_get_and_set_default_options()
    {
        $defaultOptions = ['some_option_key' => 'some_option_value'];
        $this->setOptions($defaultOptions)->getOptions()->shouldReturn($defaultOptions);
    }

    public function it_should_add_default_options()
    {
        $defaultOptions = ['headers' => ['User-Agent' => 'guzzle_api_spec_test']];
        $fakeRequestOptions = ['some_options' => 'some_value'];
        $this->setOptions($defaultOptions);
        $this->addDefaultOptions($fakeRequestOptions)->shouldReturn(array_merge($fakeRequestOptions, $defaultOptions));
    }

    public function it_should_add_default_options_when_making_a_call($client, $request)
    {
        $options = ['headers' => ['User-Agent' => 'guzzle_api_spec_test']];
        $request->getOptions()->shouldBeCalled()->willReturn([]);
        $request->setOptions(Argument::type('array'))->shouldBeCalled();
        $client->makeCall(
            $request->getWrappedObject()->getFullUrl(),
            $request->getWrappedObject()->getHeaders(),
            []
        )->shouldBeCalled()->willReturn(['headers' => [], 'status' => 200, 'body' => '{"pubstatus": "usable", "_links": {"parent": {"href": "/", "title": "home"}, "collection": {"href": "items", "title": "items"}, "self": {"href": "items/tag:example.com,0001:newsml_BRE9A607", "title": "Item"}}, "body_text": "Andromeda and Milky Way will collide in about 2 billion years", "type": "text", "language": "en", "versioncreated": "2015-03-09T16:32:23+0000", "uri": "http://api.master.dev.superdesk.org/items/tag%3Aexample.com%2C0001%3Anewsml_BRE9A607", "version": "2", "headline": "Andromeda on a collision course"}']);
        $this->makeApiCall($request);
    }
}
