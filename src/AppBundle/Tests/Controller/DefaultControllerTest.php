<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

class DefaultControllerTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        $filesystem = new Filesystem();
        $filesystem->mirror(__DIR__.'/../Fixtures/theme_1', __DIR__.'/../../../../app/Resources/themes/theme_test');
    }

    public static function tearDownAfterClass()
    {
        $filesystem = new Filesystem();
        $filesystem->remove(__DIR__.'/../../../../app/Resources/themes/theme_test');
    }

    public function testIndexOnPhone()
    {
        $client = static::createClient();
        $client->setServerParameters(array(
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5'
        ));

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("theme_test/phone/index.html.twig")')->count() > 0);
    }

    public function testIndexOnTablet()
    {
        $client = static::createClient();
        $client->setServerParameters(array(
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (iPad; U; CPU OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5'
        ));

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("theme_test/tablet/index.html.twig")')->count() > 0);
    }

    public function testIndexOnDesktop()
    {
        $client = static::createClient();
        $client->setServerParameters(array(
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:8.0.1) Gecko/20100101 Firefox/8.0.1 FirePHP/0.6'
        ));

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("theme_test/desktop/index.html.twig")')->count() > 0);
    }

    public function testIndexWithoutUserAgent()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("theme_test/desktop/index.html.twig")')->count() > 0);
    }

    public function testIndexWithoutDeviceTemplate()
    {
        $filesystem = new Filesystem();
        $filesystem->remove(__DIR__.'/../../../../app/Resources/themes/theme_test/desktop/index.html.twig');

        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Homepage theme_test")')->count() > 0);
    }
}
