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
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SubscriptionLoaderTest extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function setUp(): void
    {
        self::bootKernel();
        $this->initDatabase();

        $this->loadCustomFixtures(['tenant']);
        $this->twig = $this->getContainer()->get('twig');
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

    public function testFilteringSubscriptionByArticleIdAndRouteId(): void
    {
        $this->logInUser();

        $template = '{% gimme subscription with { user: app.user, articleId: 20, routeId: 10 } %} {{ subscription.code }} {{ subscription.type }} {{ subscription.active }} {% endgimme %}';
        $result = $this->getRendered($template);

        self::assertEquals(' 14 recurring 1 ', $result);
    }

    public function testFilteringSubscriptionByNameAndArticleId(): void
    {
        $this->logInUser();

        $template = '{% gimme subscription with { user: app.user, articleId: 20, name: "premium_content" } %} {{ subscription.code }} {{ subscription.type }} {{ subscription.active }} {{ subscription.details.name }} {% endgimme %}';
        $result = $this->getRendered($template);

        self::assertEquals(' 12 recurring 1 premium_content ', $result);
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
