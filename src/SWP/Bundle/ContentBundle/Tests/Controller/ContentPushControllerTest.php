<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class ContentPushControllerTest extends WebTestCase
{
    private $router;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:doctrine:schema:update', ['--force' => true, '--env' => 'test'], true);

        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/tenant.yml',
        ]);

        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);

        $this->router = $this->getContainer()->get('router');
    }

    public function testContentPush()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'articles',
                'type' => 'collection',
                'parent' => '/',
                'content' => null,
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"versioncreated":"2016-05-25T11:53:15+0000","pubstatus":"usable","guid":"urn:newsml:localhost:2016-05-25T12:41:05.637675:c97f2b4a-5f09-41e0-b73d-b61d7325e5cc","headline":"ads fsadf sdaf sadf sadf","byline":"John Doe","subject":[{"name":"archaeology","code":"01001000"}],"urgency":3,"priority":6,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test package 5","version":"6","associations":{"main":{"versioncreated":"2016-05-25T11:14:52+0000","pubstatus":"usable","body_html":"<p>sadf sadf sdaf sadg dg sadfasdg sa<\/p><p>df&nbsp;<\/p><p>asd<\/p><p>fsa&nbsp;<\/p><p>f&nbsp;<\/p><p>asd f<\/p>","headline":"sadf sadf sadf\u00a0","byline":"sadfsda fsdf sadf","subject":[{"name":"archaeology","code":"01001000"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 1","priority":6,"type":"composite","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:14:51.697607:9ba267e4-fed7-4d08-9af0-36bc933eb0f4"},"story-1":{"versioncreated":"2016-05-25T11:53:14+0000","pubstatus":"usable","body_html":"<p>asd fsadf sadf sadf sda<\/p><p>fsad<\/p><p>f&nbsp;<\/p><p>sad<\/p><p>f sadf sadfsadf&nbsp;<\/p><p>lorem ipsum 3<\/p>","headline":"lorem ipsum content 3\u00a0","byline":"John Doe","subject":[{"name":"theft","code":"02001003"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 3","priority":6,"type":"composite","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:53:14.419018:b698d547-35a5-4f0f-9167-3dbecb1dae78"},"story-0":{"versioncreated":"2016-05-25T11:35:43+0000","pubstatus":"usable","body_html":"<p>lorem ispum body&nbsp;<\/p>","headline":"cinema film festival item","byline":"John Doe","subject":[{"name":"film festival","code":"01005001"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 2 ","priority":6,"type":"composite","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:35:43.450626:91228dd7-853e-41c6-8bea-32b75496c618"}},"type":"composite","language":"en"}');

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => 'articles']), [
            'route' => [
                'content' => 'ads-fsadf-sdaf-sadf-sadf',
            ],
        ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'ads-fsadf-sdaf-sadf-sadf']), [
            'article' => [
                'status' => 'published',
            ],
        ]);

        $client->request('GET', '/articles/ads-fsadf-sdaf-sadf-sadf');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(
            'ads-fsadf-sdaf-sadf-sadf',
            $client->getResponse()->getContent()
        );
    }
}
