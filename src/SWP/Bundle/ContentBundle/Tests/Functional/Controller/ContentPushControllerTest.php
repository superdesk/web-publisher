<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests\Functional\Controller;

use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\RouterInterface;

final class ContentPushControllerTest extends WebTestCase
{
    const TEST_CONTENT = '{"versioncreated":"2016-05-25T11:53:15+0000","firstcreated":"2016-05-25T10:23:15+0000","pubstatus":"usable","guid":"urn:newsml:localhost:2016-05-25T12:41:05.637675:c97f2b4a-5f09-41e0-b73d-b61d7325e5cc","headline":"test package 5","byline":"John Doe","subject":[{"name":"archaeology","code":"01001000"}],"urgency":3,"priority":6,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"ads fsadf sdaf sadf sadf","version":"6","associations":{"main":{"versioncreated":"2016-05-25T11:14:52+0000","pubstatus":"usable","body_html":"<p>sadf sadf sdaf sadg dg sadfasdg sa<\/p><p>df&nbsp;<\/p><p>asd<\/p><p>fsa&nbsp;<\/p><p>f&nbsp;<\/p><p>asd f<\/p>","headline":"sadf sadf sadf\u00a0","byline":"sadfsda fsdf sadf","subject":[{"name":"archaeology","code":"01001000"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 1","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:14:51.697607:9ba267e4-fed7-4d08-9af0-36bc933eb0f4"},"story-1":{"versioncreated":"2016-05-25T11:53:14+0000","pubstatus":"usable","body_html":"<p>asd fsadf sadf sadf sda<\/p><p>fsad<\/p><p>f&nbsp;<\/p><p>sad<\/p><p>f sadf sadfsadf&nbsp;<\/p><p>lorem ipsum 3<\/p>","headline":"lorem ipsum content 3\u00a0","byline":"John Doe","subject":[{"name":"theft","code":"02001003"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 3","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:53:14.419018:b698d547-35a5-4f0f-9167-3dbecb1dae78"},"story-0":{"versioncreated":"2016-05-25T11:35:43+0000","pubstatus":"usable","body_html":"<p>lorem ispum body&nbsp;<\/p>","headline":"cinema film festival item","description_text": "test abstract","byline":"John Doe","subject":[{"name":"film festival","code":"01005001"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 2 ","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:35:43.450626:91228dd7-853e-41c6-8bea-32b75496c618"}},"type":"composite","language":"en"}';

    private $router;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        self::bootKernel();
        $this->initDatabase();
        /* @var RouterInterface router */
        $this->router = $this->getContainer()->get('router');
    }

    /**
     * @covers \SWP\Bundle\ContentBundle\Controller\ContentPushController::pushAssetsAction
     */
    public function testAssetsPush()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getContainer()->getParameter('kernel.cache_dir').'/uploads');

        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            [
                'mediaId' => 'asdgsadfvasdf4w35qwetasftest'],[
                'media' => new UploadedFile(__DIR__.'/../app/Resources/test_file.png', 'test_file.png', 'image/png', null, true),
            ]
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            [
                'media_id' => 'asdgsadfvasdf4w35qwetasftest',
                'URL' => 'http://localhost/uploads/swp/media/asdgsadfvasdf4w35qwetasftest.png',
                'media' => base64_encode(file_get_contents(__DIR__.'/../app/Resources/test_file.png')),
                'mime_type' => 'image/png',
                'filemeta' => [],
            ],
            json_decode($client->getResponse()->getContent(), true)
        );

        // Test amazon mediaId format
        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            [
                'mediaId' => '2016083108080/6c182d783f51c4654c5feb8491600917ec38dc8675d44b886d7e03a897d9bee7.jpg'],[
                'media' => new UploadedFile(__DIR__.'/../app/Resources/test_file.png', 'test_file.png', 'image/png', null, true),
            ]
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            [
                'media_id' => '2016083108080/6c182d783f51c4654c5feb8491600917ec38dc8675d44b886d7e03a897d9bee7.jpg',
                'URL' => 'http://localhost/uploads/swp/media/2016083108080_6c182d783f51c4654c5feb8491600917ec38dc8675d44b886d7e03a897d9bee7.png',
                'media' => base64_encode(file_get_contents(__DIR__.'/../app/Resources/test_file.png')),
                'mime_type' => 'image/png',
                'filemeta' => [],
            ],
            json_decode($client->getResponse()->getContent(), true)
        );

        // Test amazon mediaId format
        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            [
                'mediaId' => 'testinstance/2016083108080/7c182d783f51c4654c5feb8491600917ec38dc8675d44b886d7e03a897d9bee7.jpg',
                'media' => new UploadedFile(__DIR__.'/../app/Resources/test_file.png', 'test_file.png', 'image/png', 3992, null, true),
            ]
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            [
                'media_id' => 'testinstance/2016083108080/7c182d783f51c4654c5feb8491600917ec38dc8675d44b886d7e03a897d9bee7.jpg',
                'URL' => 'http://localhost/uploads/swp/media/testinstance_2016083108080_7c182d783f51c4654c5feb8491600917ec38dc8675d44b886d7e03a897d9bee7.png',
                'media' => base64_encode(file_get_contents(__DIR__.'/../app/Resources/test_file.png')),
                'mime_type' => 'image/png',
                'filemeta' => [],
            ],
            json_decode($client->getResponse()->getContent(), true)
        );
    }
}
