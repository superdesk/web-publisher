<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\Controller;

use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\RouterInterface;

class RegistrationControllerTest extends WebTestCase
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**x
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);
        $this->router = $this->getContainer()->get('router');
    }

    public function testRegistration()
    {
        $client = static::createClient();
        $client->enableProfiler();
        $client->request('POST', $this->router->generate('swp_api_core_register_user'), [
            'email' => 'contact@example.com',
            'username' => 'sofab.contact',
            'plainPassword' => [
                'first' => 'testPass',
                'second' => 'testPass',
            ],
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        /** @var \Symfony\Component\Mailer\DataCollector\MessageDataCollector $mailerCollector */
        $mailCollector = $client->getProfile()->getCollector('mailer');
        $this->assertEmailCount(1);

        /** @var \Symfony\Bridge\Twig\Mime\TemplatedEmail $messageBody */
        $message = $this->getMailerMessage(0);

        // Asserting email data
        $this->assertInstanceOf(TemplatedEmail::class, $message);
        $this->assertEquals('Welcome sofab.contact!', $message->getSubject());
        $this->assertEquals('contact@localhost', $message->getFrom()[0]->getAddress());
        $this->assertEquals('contact@example.com', $message->getTo()[0]->getAddress());

        preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $message->getHtmlBody(), $match);
        $client->request('GET', $match[0][0]);

        self::assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();
        self::assertTrue($client->getResponse()->isSuccessful());
        self::assertContains('The user has been created successfully.', $client->getResponse()->getContent());
    }

    public function testValidation()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_register_user'), [
            'email' => 'notemail',
            'username' => '',
            'plainPassword' => [
                'first' => 'testPass',
                'second' => 'testPasss',
            ],
        ]);

        self::assertEquals(400, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('errors', $content);
        self::assertEquals('The email is not valid.', $content['errors']['children']['email']['errors'][0]);
        self::assertEquals('Please enter a username.', $content['errors']['children']['username']['errors'][0]);
        self::assertEquals('The entered passwords don\'t match.', $content['errors']['children']['plainPassword']['children']['first']['errors'][0]);
    }
}
