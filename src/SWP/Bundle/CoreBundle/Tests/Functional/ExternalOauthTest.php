<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Tests\Functional;

use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

final class ExternalOauthTest extends WebTestCase
{
    private $router;

    public function setUp(): void
    {
        self::bootKernel();

        $this->initDatabase();
        $this->loadCustomFixtures(['user']);

        $this->router = $this->getContainer()->get('router');
    }

    public function testAuthorizationExistingUser()
    {
        $client = static::createClient();

        $client->request('GET', $this->router->generate('connect_oauth_start'));
        self::assertEquals(301, $client->getResponse()->getStatusCode());
    }
}
