<?php

namespace spec\SWP\Bundle\FacebookInstantArticlesBundle\Manager;

use Facebook\Authentication\AccessToken;
use Facebook\Authentication\OAuth2Client;
use Facebook\Facebook;
use Facebook\FacebookResponse;
use Facebook\GraphNodes\Collection;
use Facebook\GraphNodes\GraphEdge;
use Facebook\GraphNodes\GraphNode;
use Facebook\Helpers\FacebookRedirectLoginHelper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FacebookInstantArticlesManagerSpec extends ObjectBehavior
{
    protected $facebook;

    public function let(
        Facebook $facebook,
        FacebookRedirectLoginHelper $loginHelper,
        AccessToken $accessToken,
        OAuth2Client $OAuth2Client
    ) {
        $this->facebook = $facebook;
        $loginHelper->getAccessToken()->willReturn($accessToken);

        $this->facebook->getOAuth2Client()->willReturn($OAuth2Client);
        $this->facebook->getRedirectLoginHelper()->willReturn($loginHelper);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Bundle\FacebookInstantArticlesBundle\Manager\FacebookInstantArticlesManager');
    }

    public function it_should_get_page_access_token(
        FacebookResponse $facebookResponse,
        GraphEdge $graphEdge,
        GraphNode $graphNode,
        Collection $collection
    ) {
        $pages = [
            0 => new Collection([
                'id' => '42312453246345234653245',
                'access_token' => 'sdfsadfsadfasdgsfgsadf',
            ]),
        ];
        $graphNode->getIterator()->willReturn(new Collection($pages));
        $collection->getIterator()->willReturn($graphNode);
        $graphEdge->getIterator()->willReturn($collection);
        $facebookResponse->getGraphEdge()->willReturn($graphEdge);

        $this->facebook->setDefaultAccessToken(Argument::type('null'))->shouldBeCalled();
        $this->facebook->get(Argument::cetera())->willReturn($facebookResponse);

        $this->getPageAccessToken($this->facebook, '42312453246345234653245')->shouldReturn('sdfsadfsadfasdgsfgsadf');
    }

    public function it_shoudl_return_collection_of_pages_and_tokens(
        FacebookResponse $facebookResponse,
        GraphEdge $graphEdge,
        Collection $collection
    ) {
        $this->facebook->setDefaultAccessToken(Argument::type('null'))->shouldBeCalled();
        $graphEdge->getIterator()->willReturn($collection);
        $facebookResponse->getGraphEdge()->willReturn($graphEdge);
        $this->facebook->get(Argument::cetera())->willReturn($facebookResponse);

        $this->getPagesAndTokens($this->facebook)->shouldReturnAnInstanceOf(GraphEdge::class);
    }
}
