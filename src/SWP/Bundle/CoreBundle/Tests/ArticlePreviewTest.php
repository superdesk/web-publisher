<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests;

use SWP\Bundle\CoreBundle\Model\UserInterface;
use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class ArticlePreviewTest extends WebTestCase
{
    private $client;
    private $router;

    public function setUp()
    {
        self::bootKernel();
        $this->client = static::createClient();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);
        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/article_preview.yml',
        ], true);
        $this->router = $this->getContainer()->get('router');
    }

    public function testArticlePreview()
    {
        $this->createAndAssignRouteToArticle();

        $this->client->request('GET', '/news/art1-not-published');

        self::assertFalse($this->client->getResponse()->isSuccessful());
        self::assertEquals(404, $this->client->getResponse()->getStatusCode());

        $this->logIn('test.user');

        $crawler = $this->client->request('GET', '/news/art1-not-published');
        self::assertTrue($this->client->getResponse()->isSuccessful());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Slug: art1-not-published")')->count());
    }

    public function testArticlePreviewWhenUserIsNotAllowedToPreview()
    {
        $this->createAndAssignRouteToArticle();

        $this->logIn('test.user', ['ROLE_USER']);
        $this->client->request('GET', '/news/art1-not-published');

        self::assertFalse($this->client->getResponse()->isSuccessful());
        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    private function createAndAssignRouteToArticle()
    {
        $this->client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'news',
                'type' => 'collection',
                'content' => null,
                'templateName' => 'news.html.twig',
                'articlesTemplateName' => 'article.html.twig',
            ],
        ]);

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        $routeContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->request('PATCH', $this->router->generate('swp_api_core_update_organization_articles', ['id' => 1]), [
            'article' => [
                'route' => $routeContent['id'],
            ],
        ]);

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    private function logIn(string $username, array $roles = [])
    {
        $session = $this->client->getContainer()->get('session');
        $firewall = 'main';

        /** @var UserInterface $user */
        $user = $this->getContainer()->get('swp.repository.user')->findOneByUsername($username);

        if (null === $user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" not found.', $username));
        }

        $token = new UsernamePasswordToken($user, null, $firewall, !empty($roles) ? $roles : $user->getRoles());
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
