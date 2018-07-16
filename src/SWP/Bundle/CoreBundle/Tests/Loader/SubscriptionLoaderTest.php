<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\Twig;

use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SubscriptionLoaderTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var RouterInterface
     */
    private $router;

    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();

        $this->loadCustomFixtures(['tenant']);
        $this->twig = $this->getContainer()->get('twig');
        $this->router = $this->getContainer()->get('router');
    }

    public function testLoadSubscriptionsByFakeUser(): void
    {
        $template = '{% gimmelist subscription from subscriptions with { user: "fake" } %} {{ subscription.id }} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertEquals('', $result);
    }

    public function testLoadSubscriptionsByExistingUser(): void
    {
        $this->logInUser();

        $template = '{% gimmelist subscription from subscriptions with { user: app.user } %} {{ subscription.code }} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertEquals(' 79 ', $result);
    }

    public function testFilteringSubscriptionsByArticleId(): void
    {
        $this->logInUser();

        $template = '{% gimmelist subscription from subscriptions with { user: app.user, articleId: 20 } %} {{ subscription.code }} {{ subscription.type }} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertEquals(' 12 recurring ', $result);
    }

    public function testFilteringSubscriptionsByArticleIdAndRouteId(): void
    {
        $this->logInUser();

        $template = '{% gimmelist subscription from subscriptions with { user: app.user, articleId: 20, routeId: 10 } %} {{ subscription.code }} {{ subscription.type }} {% endgimmelist %}';
        $result = $this->getRendered($template);

        self::assertEquals(' 14 recurring ', $result);
    }

    private function logInUser()
    {
        $user = $this->getContainer()->get('swp.repository.user')->findOneByEmail('test.user@sourcefabric.org');
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', ['ROLE_USER']);

        $tokenStorage = $this->getContainer()->get('security.token_storage');

        $tokenStorage->setToken($token);
    }

    private function getRendered($template, $context = [])
    {
        $template = $this->twig->createTemplate($template);
        $content = $template->render($context);

        return $content;
    }
}
