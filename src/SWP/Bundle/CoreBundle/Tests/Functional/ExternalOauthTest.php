<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Tests\Functional;

use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

final class ExternalOauthTest extends WebTestCase
{
    private $router;

    public function setUp(): void
    {
        parent::setUp();

        $this->initDatabase();
        //$this->loadCustomFixtures(['user']);

        $this->router = $this->getContainer()->get('router');
    }

    public function testConnectStart(): void
    {
        $client = static::createClient();

        $client->request('GET', $this->router->generate('connect_oauth_start'));
        self::assertEquals(302, $client->getResponse()->getStatusCode());
        $authorizeUrl = $client->getResponse()->headers->all()['location'][0];
        self::assertContains('/authorize', $authorizeUrl);
    }

    private function authorizeWithCode(string $code): Client
    {
        $client = static::createClient([], [
            'HTTP_Authorization' => null,
        ]);

        // Initalize and get the parameters
        $client->request('GET', $this->router->generate('connect_oauth_start'));
        $authorizeUrl = $client->getResponse()->headers->all()['location'][0];

        // State is the important part here, our mock auth server doesn't care,
        // but the local OAuth library will check that the state is the same
        // as it sets in the redirect to the oauth server.
        $query_str = \parse_url($authorizeUrl, PHP_URL_QUERY);
        \parse_str($query_str, $query);
        $state = $query['state'];

        // We skip the request to /authorize, this part is between the client and the oauth server, and the result
        // is an authorization code. We instead use mock auth codes for our mock server, which return different
        // access tokens for different users depending on the authorization code.
        $client->request('GET', $this->router->generate('connect_oauth_check').'?code='.$code.'&state='.$state);

        return $client;
    }

    public function testAuthorizeExistingUser(): void
    {
        $client = $this->authorizeWithCode('123');
        self::assertEquals(302, $client->getResponse()->getStatusCode());
        // Make sure the correct user was logged in
        $client->request('GET', $this->router->generate('homepage'));
        self::assertContains('superdesk.test.user@sourcefabric.org', $client->getResponse()->getContent());
    }

    public function testAuthorizeExistingUserNewEmail(): void
    {
        $client = $this->authorizeWithCode('321');
        self::assertEquals(302, $client->getResponse()->getStatusCode());
        // Make sure the correct user was logged in
        $client->request('GET', $this->router->generate('homepage'));
        self::assertContains('new.email@example.com', $client->getResponse()->getContent());
    }

    public function testAuthorizeNewUser(): void
    {
        $client = $this->authorizeWithCode('132');
        self::assertEquals(302, $client->getResponse()->getStatusCode());
        // Make sure the correct user was logged in
        $client->request('GET', $this->router->generate('homepage'));
        self::assertContains('new.user@example.com', $client->getResponse()->getContent());
    }

    public function testWrongAuthCode(): void
    {
        $client = $this->authorizeWithCode('231');
        // Make sure we get unauthorized response when using an invalid authorization code.
        self::assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
